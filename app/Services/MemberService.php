<?php

namespace App\Services;

use App\Exceptions\InvalidRequestException;
use App\Models\Api;
use App\Models\FsLevel;
use App\Models\GameRecord;
use App\Models\Member;
use App\Models\MemberLog;
use App\Models\MemberMoneyLog;
use App\Models\TransactionHistory;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class MemberService
{
    const DEMO_NUMBER_LENGTH = 4;

    public function getDemoPrefix()
    {
        return substr(md5(env('APP_URL')), 0, 5);
    }

    public function getLastDemoName()
    {
        return Member::withTrashed()->where('is_demo', 1)->latest()->orderByDesc('id')->first()->name ?? '';
    }

    public function generateDemoName()
    {
        $last = $this->getLastDemoName();

        $prefix = $name = $this->getDemoPrefix();
        $number = 0;

        if ($last) {
            $number = intval(substr($last, strlen($prefix)));
        }

        return $name . $this->getDemoNameByNumber($number + 1);
    }

    public function getDemoNameByNumber($number)
    {
        return str_pad($number, self::DEMO_NUMBER_LENGTH, 0, STR_PAD_LEFT);
    }

    public function updateMemberML(Member $member)
    {
        $total_ml = GameRecord::where('member_id', $member->id)
            ->where('status', '<>', GameRecord::STATUS_X)
            ->where('is_ml_use', 0)
            ->sum('validBetAmount');

        try {
            DB::transaction(function () use ($member, $total_ml) {
                $add_ml = sprintf("%.2f", $total_ml);
                $member->decrement('ml_money', $add_ml);
                $member->increment('total_money', $total_ml);

                GameRecord::where('member_id', $member->id)->where('status', '<>', GameRecord::STATUS_X)->update(['is_ml_use' => 1]);
            });
        } catch (\Exception $e) {
            DB::rollBack();
            throw new InvalidRequestException(trans('res.api.drawing.ml_calc_err') . $e->getMessage());
        }
    }

    public function checkMemberTransferError()
    {
        $logs = MemberLog::where('status', MemberLog::STATUS_NOT_DEAL)->get();

        $count = 0;
        $errMsg = '';

        if ($logs->count() == 0) {
            return ['code' => 0, 'data' => $count, 'msg' => $errMsg];
        }

        $services = app(SelfService::class);

        $now = Carbon::now();

        foreach ($logs as $item) {
            $member = $item->member;

            $json = $services->checktransfer($member, $now, $item->remark);

            try {
                $res = json_decode($json, 1);

                if (!is_array($res)) {
                    throw new InvalidRequestException('网络错误，请重试');
                }

                if ($res['status']['errorCode']) {
                    throw new InvalidRequestException('错误代码：' . $res['status']['errorCode'] . '，错误信息：' . $res['status']['msg']);
                }

                if (count($res['data']) > 0 && Arr::get(current($res['data']), 'bill_no') && current($res['data'])['bill_no'] != $item->remark) {
                    echo '会员【' . $item->member->name . '】订单号【' . $item->remark . '】分数未丢失' . PHP_EOL;
                } else {
                    $count++;

                    DB::transaction(function () use ($item, $member) {
                        $transfer = Transfer::where('bill_no', $item->remark)->first();

                        $api = null;
                        if ($transfer) {
                            $api = Api::where('api_name', $transfer->api_name)->first();
                        }

                        if (!$transfer || !$api) {
                            throw new InvalidRequestException('无法查询到本地转账记录');
                        }

                        $money_type = $transfer->money_type;

                        $member->increment($money_type, $transfer->money);

                        MemberMoneyLog::create([
                            'member_id' => $member->id,
                            'money' => $transfer->money,
                            'money_before' => $member->$money_type,
                            'money_after' => $member->$money_type + $transfer->money,
                            'number_type' => MemberMoneyLog::MONEY_TYPE_ADD,
                            'operate_type' => MemberMoneyLog::OPERATE_TYPE_DEPOSIT_RETURN,
                            'money_type' => $money_type,
                            'description' => '转入【' . $api->api_title . '】游戏失败，退还账户金额【' . $transfer->money . '元】'
                        ]);

                        echo '会员【' . $item->member->name . '】补单订单号【' . $item->remark . '】' . PHP_EOL;
                    });
                }

            } catch (\Exception $e) {
                DB::rollBack();
                $errMsg = $errMsg . ',' . $e->getMessage();
            }
        }

        return ['code' => 1, 'data' => $count, 'msg' => $errMsg];
    }

    public function getFsSbo($member, $params = [])
    {
        $from = data_get($params, 'created_at_from');
        $to = data_get($params, 'created_at_to');

        $end = data_get($params, 'end');
        $isFsAll = data_get($params, 'is_fs_all', false);

        $fsLevel = FsLevel::where('fs_levels.type', FsLevel::TYPE_SYSTEM)
            ->where('fs_levels.lang', $member->lang);
            // ->where('fs_levels.level', $member->level);

        // if ($fsLevel->distinct()->count() < count(array_keys(TransactionHistory::getProductType()))) {
        //     return collect(['data' => collect([]), 'fs_level' => []]);
        // }

        if ($fsLevel->distinct()->count() < 1) {
            return collect(['data' => collect([]), 'fs_level' => []]);
        }
        
        $histories = TransactionHistory::select(DB::raw('transaction_histories.amount, transaction_histories.product_type, transaction_histories.transaction_id'))
            ->where('transaction_histories.member_id', $member->id)
            ->where('transaction_histories.amount', '>', 0)
            // ->whereNotIn('transaction_histories.game_provider', [TransactionHistory::GP_SABA_SPORTS, TransactionHistory::GP_AFB_SPORTS, TransactionHistory::GP_BTI_SPORTS])
            ->when($isFsAll, function ($query) {
                $query->whereIn('transaction_histories.is_fs', [TransactionHistory::IS_FS_OFF, TransactionHistory::IS_FS_ON]);
            }, function ($query) {
                $query->where('transaction_histories.is_fs', TransactionHistory::IS_FS_OFF);
            })
            ->whereIn('transaction_histories.status', [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST])
            // ->when($end, function ($query) use ($end) {
            //     $query->where('transaction_histories.created_at', '<', date('Y-m-d H:i:s', $end));
            // })
            // add created_at_from and created_at_to
            ->when($from, function ($query) use ($from) {
                $query->where('transaction_histories.transaction_time', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->where('transaction_histories.transaction_time', '<=', $to);
            });

        $histories = $histories->get()->transform(function ($item){
            if ($item->fs_detail == null) {
                return [];
            }
            
            $fsDetail = json_decode($item->fs_detail);
            $fsRate = data_get($fsDetail, 'fs_rate');

            $item->rate = $fsRate;
            $item->fs_money = floatval(sprintf("%.2f", $item->amount * $fsRate / 100));
            $item->game_type_text = $item->getGameProviderText();
            return $item;
        });

        return [
            'data' => $histories->filter(function ($value) {
                return $value;
            }),
            'fs_level' => $fsLevel->first()
        ];
    }

    public function getFsSboSaba($member, $params = [])
    {
        $from = data_get($params, 'created_at_from');
        $to = data_get($params, 'created_at_to');

        $end = data_get($params, 'end');
        $isFsAll = data_get($params, 'is_fs_all', false);

        $fsLevel = FsLevel::where('fs_levels.type', FsLevel::TYPE_SABA_SYSTEM)
            ->where('fs_levels.lang', $member->lang);
            // ->where('fs_levels.level', $member->level);

        if ($fsLevel->distinct()->count() < 1) {
            return collect(['data' => collect([]), 'fs_level' => []]);
        }

        $histories = TransactionHistory::select(DB::raw('transaction_histories.amount, transaction_histories.product_type, transaction_histories.game_provider, transaction_histories.transaction_id'))
            ->where('transaction_histories.member_id', $member->id)
            ->where('transaction_histories.amount', '>', 0)
            ->where('transaction_histories.product_type', '=', TransactionHistory::PT_SEAMLESS_GAME)
            ->where('transaction_histories.game_provider', '=', TransactionHistory::GP_SABA_SPORTS)
            ->when($isFsAll, function ($query) {
                $query->whereIn('transaction_histories.is_fs', [TransactionHistory::IS_FS_OFF, TransactionHistory::IS_FS_ON]);
            }, function ($query) {
                $query->where('transaction_histories.is_fs', TransactionHistory::IS_FS_OFF);
            })
            ->whereIn('transaction_histories.status', [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST])
            // ->when($end, function ($query) use ($end) {
            //     $query->where('transaction_histories.created_at', '<', date('Y-m-d H:i:s', $end));
            // })
            // add created_at_from and created_at_to
            ->when($from, function ($query) use ($from) {
                $query->where('transaction_histories.transaction_time', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->where('transaction_histories.transaction_time', '<=', $to);
            });

        $histories = $histories->get()->transform(function ($item){
            if ($item->fs_detail == null) {
                return [];
            }
            
            $fsDetail = json_decode($item->fs_detail);
            $fsRate = data_get($fsDetail, 'fs_rate');

            $item->rate = $fsRate;
            $item->fs_money = floatval(sprintf("%.2f", $item->amount * $fsRate / 100));
            $item->game_type_text = $item->getGameProviderText();
            return $item;
        });

        return [
            'data' => $histories->filter(function ($value) {
                return $value;
            }),
            'fs_level' => $fsLevel->first()
        ];
    }

    public function getFsSboAfb($member, $params = [])
    {
        $from = data_get($params, 'created_at_from');
        $to = data_get($params, 'created_at_to');

        $end = data_get($params, 'end');
        $isFsAll = data_get($params, 'is_fs_all', false);

        $fsLevel = FsLevel::where('fs_levels.type', FsLevel::TYPE_AFB_SYSTEM)
            ->where('fs_levels.lang', $member->lang);
            // ->where('fs_levels.level', $member->level);

        if ($fsLevel->distinct()->count() < 1) {
            return collect(['data' => collect([]), 'fs_level' => []]);
        }

        $histories = TransactionHistory::select(DB::raw('transaction_histories.amount, transaction_histories.product_type, transaction_histories.game_provider, transaction_histories.transaction_id, transaction_histories.fs_detail'))
            ->where('transaction_histories.member_id', $member->id)
            ->where('transaction_histories.amount', '>', 0)
            ->where('transaction_histories.product_type', '=', TransactionHistory::PT_SEAMLESS_GAME)
            ->where('transaction_histories.game_provider', '=', TransactionHistory::GP_AFB_SPORTS)
            ->when($isFsAll, function ($query) {
                $query->whereIn('transaction_histories.is_fs', [TransactionHistory::IS_FS_OFF, TransactionHistory::IS_FS_ON]);
            }, function ($query) {
                $query->where('transaction_histories.is_fs', TransactionHistory::IS_FS_OFF);
            })
            ->whereIn('transaction_histories.status', [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST])
            // ->when($end, function ($query) use ($end) {
            //     $query->where('transaction_histories.created_at', '<', date('Y-m-d H:i:s', $end));
            // })
            // add created_at_from and created_at_to
            ->when($from, function ($query) use ($from) {
                $query->where('transaction_histories.created_at', '>=', date_format(date_create($from), 'Y-m-d H:i:s'));
            })
            ->when($to, function ($query) use ($to) {
                $query->where('transaction_histories.created_at', '<=', date_format(date_create($to), 'Y-m-d H:i:s'));
            });
        
        $histories = $histories->get()->transform(function ($item){
            if ($item->fs_detail == null) {
                return [];
            }
            
            $fsDetail = json_decode($item->fs_detail);
            $fsRate = data_get($fsDetail, 'fs_rate');

            $item->rate = $fsRate;
            $item->fs_money = floatval(sprintf("%.2f", $item->amount * $fsRate / 100));
            $item->game_type_text = $item->getGameProviderText();
            return $item;
        });
        return [
            'data' => $histories->filter(function ($value) {
                return $value;
            }),
            'fs_level' => $fsLevel->first()
        ];
    }

    public function getFsSboBti($member, $params = [])
    {
        $from = data_get($params, 'created_at_from');
        $to = data_get($params, 'created_at_to');
        
        $end = data_get($params, 'end');
        $isFsAll = data_get($params, 'is_fs_all', false);

        $fsLevel = FsLevel::where('fs_levels.type', FsLevel::TYPE_BTI_SYSTEM)
            ->where('fs_levels.lang', $member->lang);
            // ->where('fs_levels.level', $member->level);

        if ($fsLevel->distinct()->count() < 1) {
            return collect(['data' => collect([]), 'fs_level' => []]);
        }

        $histories = TransactionHistory::select(DB::raw('transaction_histories.amount, transaction_histories.product_type, transaction_histories.game_provider, transaction_histories.transaction_id'))
            ->where('transaction_histories.member_id', $member->id)
            ->where('transaction_histories.amount', '>', 0)
            ->where('transaction_histories.product_type', '=', TransactionHistory::PT_SEAMLESS_GAME)
            ->where('transaction_histories.game_provider', '=', TransactionHistory::GP_BTI_SPORTS)
            ->when($isFsAll, function ($query) {
                $query->whereIn('transaction_histories.is_fs', [TransactionHistory::IS_FS_OFF, TransactionHistory::IS_FS_ON]);
            }, function ($query) {
                $query->where('transaction_histories.is_fs', TransactionHistory::IS_FS_OFF);
            })
            ->whereIn('transaction_histories.status', [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST])
            // ->when($end, function ($query) use ($end) {
            //     $query->where('transaction_histories.created_at', '<', date('Y-m-d H:i:s', $end));
            // })
            // add created_at_from and created_at_to
            ->when($from, function ($query) use ($from) {
                $query->where('transaction_histories.transaction_time', '>=', $from);
            })
            ->when($to, function ($query) use ($to) {
                $query->where('transaction_histories.transaction_time', '<=', $to);
            });

        $histories = $histories->get()->transform(function ($item){
            if ($item->fs_detail == null) {
                return [];
            }
            
            $fsDetail = json_decode($item->fs_detail);
            $fsRate = data_get($fsDetail, 'fs_rate');

            $item->rate = $fsRate;
            $item->fs_money = floatval(sprintf("%.2f", $item->amount * $fsRate / 100));
            $item->game_type_text = $item->getGameProviderText();
            return $item;
        });

        return [
            'data' => $histories->filter(function ($value) {
                return $value;
            }),
            'fs_level' => $fsLevel->first()
        ];
    }

    public function getTransactions($member, $params = [])
    {
        return DB::table('transaction_histories')
            ->select(DB::raw('sum(amount) as total_transaction'))
            ->where('member_id', $member->id)
            ->where('amount', '>', 0)
            ->whereIn('status', [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST])
            ->first();
    }
}
