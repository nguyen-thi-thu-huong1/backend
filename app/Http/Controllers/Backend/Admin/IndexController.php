<?php

namespace App\Http\Controllers\Backend\Admin;

use Carbon\Carbon;
use App\Models\Base;
use App\Models\Member;
use App\Models\Drawing;
use App\Models\Message;
use App\Models\Recharge;
use Carbon\CarbonPeriod;
use App\Models\MemberLog;
use App\Models\GameRecord;
use App\Models\SystemConfig;
use Illuminate\Http\Request;
use App\Models\ActivityApply;
use App\Services\GameService;
use App\Services\MenuService;
use App\Models\MemberMoneyLog;
use App\Services\AgentService;
use App\Models\CreditPayRecord;
use App\Models\MemberAgentApply;
use App\Models\MemberYuebaoPlan;
use App\Services\ActivityService;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Backend\Admin\MembersController;

class IndexController extends AdminBaseController
{

    public function main()
    {
        $voice_list = SystemConfig::where('config_group', 'notice')
            ->where('type', 'file')->where('value', '<>', '')->get();

        $user = $this->guard()->user();
        return view("admin.main", compact('voice_list', 'user'));
    }

    public function index(Request $request)
    {
        // 获取统计数据
        $startDay = Carbon::now()->startOfDay();
        $startMonth = Carbon::now()->startOfMonth();

        // 注册数据
        $today_register = Member::where('created_at', '>', $startDay)->filterDemoAccount()->count();
        $month_register = Member::where('created_at', '>', $startMonth)->filterDemoAccount()->count();

        // 营销数据（包括 会员的福利金额，代理的佣金 和 全民代理的返点）
        $today_free = app(AgentService::class)->getTotalFreeMoney($startDay);
        $month_free = app(AgentService::class)->getTotalFreeMoney($startMonth);

        // 今日投注
        $today_bet = GameRecord::where('created_at', '>', $startDay)
            ->whereNotIn('member_id', Member::demoIdLists())
            ->where('status', GameRecord::STATUS_COMPLETE)
            ->sum('betAmount');

        $todaySboBet = TransactionHistory::where('created_at', '>', $startDay)
            ->whereNotIn('member_id', Member::demoIdLists())
            ->whereIn('status', [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST])
            ->sum('amount');

        $today_bet = $today_bet + $todaySboBet;

        $month_bet = GameRecord::where('created_at', '>', $startMonth)
            ->whereNotIn('member_id', Member::demoIdLists())
            ->sum('betAmount');

        $monthSboBet = TransactionHistory::where('created_at', '>', $startMonth)
            ->whereNotIn('member_id', Member::demoIdLists())
            ->whereIn('status', [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST])
            ->sum('amount');

        $month_bet = $month_bet + $monthSboBet;

        // 游戏营收 (派彩金额 - 投注金额)
        $today_game_profit = GameRecord::where('created_at', '>', $startDay)
            ->whereNotIn('member_id', Member::demoIdLists())
            ->sum('netAmount');

        $todaySboProfit = TransactionHistory::where('created_at', '>', $startDay)
            ->whereNotIn('member_id', Member::demoIdLists())
            ->whereIn('status', [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST])
            ->sum(DB::raw('win_loss - amount'));

        $today_game_profit = $today_game_profit + $todaySboProfit;

        $today_game_profit = sprintf("%.2f", $today_game_profit - $today_bet);

        $month_game_profit = GameRecord::where('created_at', '>', $startMonth)
            ->whereNotIn('member_id', Member::demoIdLists())
            ->where('status', GameRecord::STATUS_COMPLETE)
            ->sum('netAmount');

        $monthSboProfit = TransactionHistory::where('created_at', '>', $startMonth)
            ->whereNotIn('member_id', Member::demoIdLists())
            ->whereIn('status', [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST])
            ->sum(DB::raw('win_loss - amount'));

        $month_game_profit = $month_game_profit + $monthSboProfit;

        $month_game_profit = sprintf("%.2f", $month_game_profit - $month_bet);

        // 获取最近7天的注册数据
        /**
        $last_7days_counts = DB::table('members')->select(DB::raw("count(*) as member_count, date_format(created_at, '%Y-%m-%d') as date"))->where('created_at','>', Carbon::now()->subDays(6)->startOfDay())->groupBy('date')->get();

        // 循环处理结果
        $last_7days = [];
        for($i = 0; $i < 7; $i++){
            $dates = Carbon::now()->subDays(6 - $i)->startOfDay()->format('Y-m-d');
            $datas = $last_7days_counts->where('date',$dates)->first();
            $last_7days[$dates] = $datas->member_count ?? 0;
        }
         **/

        // 获取最近10天的充值数据
        $last_10day_sum = DB::table('recharges')
            ->select(DB::raw("sum(money) as recharge_sum, date_format(created_at, '%Y-%m-%d') as date"))
            ->where('created_at', '>', Carbon::now()->subDays(9)->startOfDay())
            ->whereNotIn('member_id', Member::demoIdLists())
            ->where('status', Recharge::STATUS_SUCCESS)
            ->groupBy('date')->get();

        $last_10days = [];
        for ($i = 0; $i < 10; $i++) {
            $dates = Carbon::now()->subDays(9 - $i)->startOfDay()->format('Y-m-d');
            $datas = $last_10day_sum->where('date', $dates)->first();
            $last_10days[$dates] = floatval($datas->recharge_sum ?? 0);
        }

        // 获取最近10天的提款数据
        $last_10day_drawing_sum = DB::table('drawings')
            ->select(DB::raw("sum(money) as drawing_sum, date_format(created_at, '%Y-%m-%d') as date"))
            ->where('created_at', '>', Carbon::now()->subDays(9)->startOfDay())
            ->whereNotIn('member_id', Member::demoIdLists())
            ->where('status', Drawing::STATUS_SUCCESS)
            ->groupBy('date')->get();

        $last_10days_drawing = [];
        for ($i = 0; $i < 10; $i++) {
            $dates = Carbon::now()->subDays(9 - $i)->startOfDay()->format('Y-m-d');
            $datas = $last_10day_drawing_sum->where('date', $dates)->first();
            $last_10days_drawing[$dates] = floatval($datas->drawing_sum ?? 0);
        }

        $today['created_at'] = Carbon::now()->setTime(0, 0, 0)->format('Y-m-d H:i:s') . ' - ' . Carbon::now()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        $member_login_today = MemberLog::where($this->convertWhere($today))->count();
        $member_total = Member::query()
            ->whereNotIn('is_demo', [Member::IS_DEMO])->whereNotIn('status', [Member::STATUS_FORBIDDEN])->count();
        $online_member = MembersController::getOnlineMember()->count();
        $money_deposit_today = Recharge::where($this->convertWhere($today))->sum('money');
        $money_withdraw_today = Drawing::where($this->convertWhere($today))->sum('money');
        $money_total_today = abs($money_deposit_today - $money_withdraw_today);
        $money_total_unprocessed_today = Recharge::where($this->convertWhere($today))->where('status', Recharge::STATUS_UNDEAL)->sum('money');
        $money_total_unprocessed_withdraw_today = Drawing::where($this->convertWhere($today))->where('status', Recharge::STATUS_UNDEAL)->sum('money');
        $promotion_total_today = 'Chưa có';
        $daily_total_return_today = MemberMoneyLog::where($this->convertWhere($today))->where('operate_type', MemberMoneyLog::OPERATE_TYPE_FANSHUI)->sum('money');
        $period = CarbonPeriod::create(Carbon::now()->subDays(30)->startOfDay(), Carbon::now());

        $lose_win_30days = $this->getDataPeriodWinLoss($period);
        $dpst_wtda_rtr_30days = $this->getDataPeriodDpstWtdaRtr($period);

        $traffic_location = $this->getDataFromCountry();
        $traffic_device = $this->getDataFromDevice();

        return view("admin.index.index", compact(
            'today_register',
            'member_login_today',
            'month_register',
            'today_free',
            'month_free',
            'today_bet',
            'month_bet',
            'today_game_profit',
            'month_game_profit',
            'last_10days',
            'last_10days_drawing',
            'member_total',
            'online_member',
            'money_deposit_today',
            'money_withdraw_today',
            'money_total_today',
            'money_total_unprocessed_today',
            'money_total_unprocessed_withdraw_today',
            'promotion_total_today',
            'daily_total_return_today',
            'lose_win_30days',
            'dpst_wtda_rtr_30days',
            'traffic_location',
            'traffic_device'
        ));
    }

    public function getDataPeriodWinLoss($dataPeriod)
    {
        $dataLabels = [];
        $dataWin = [];
        $dataLoss = [];
        foreach ($dataPeriod as $date) {
            $resultTotalWin = TransactionHistory::query()
                ->select(
                    DB::raw('SUM(win_loss) as total_win_loss'),
                    DB::raw('SUM(amount) as total_amount')
                )
                ->where('status', TransactionHistory::STATUS_WIN)
                // filter by transaction_time
                ->when($date, function ($query1) use ($date) {
                    $query1->whereBetween('transaction_time', [$date, $date->clone()->endOfDay()]);
                })
                ->first();
            $resultTotalLoss = TransactionHistory::query()
                ->select(
                    DB::raw('SUM(win_loss) as total_win_loss'),
                    DB::raw('SUM(amount) as total_amount')
                )
                ->where('status', TransactionHistory::STATUS_LOST)
                // filter by transaction_time
                ->when($date, function ($query1) use ($date) {
                    $query1->whereBetween('transaction_time', [$date, $date->clone()->endOfDay()]);
                })
                ->first();

            $dataLabels[] = $date->format('d/m/Y');
            $dataWin[] = $resultTotalWin['total_win_loss'] - $resultTotalWin['total_amount'];
            $dataLoss[] = abs(-$resultTotalLoss['total_win_loss'] + $resultTotalLoss['total_amount']);
        }

        return [
            'dataLabels' => $dataLabels,
            'dataWin' => $dataWin,
            'dataLoss' => $dataLoss
        ];
    }

    public function getDataPeriodDpstWtdaRtr($dataPeriod)
    {
        $dataLabels = [];
        $dataRecharge = [];
        $dataDrawing = [];
        $dataTotal = [];

        foreach ($dataPeriod as $date) {
            $resultRecharge = Recharge::query()
                ->select(
                    DB::raw('SUM(money) as total_money')
                )
                ->where('status', Recharge::STATUS_SUCCESS)
                // filter by created_at
                ->when($date, function ($query1) use ($date) {
                    $query1->whereBetween('created_at', [$date, $date->clone()->endOfDay()]);
                })
                ->first();
            $resultDrawing = Drawing::query()
                ->select(
                    DB::raw('SUM(money) as total_money')
                )
                ->where('status', Drawing::STATUS_SUCCESS)
                // filter by created_at
                ->when($date, function ($query1) use ($date) {
                    $query1->whereBetween('created_at', [$date, $date->clone()->endOfDay()]);
                })
                ->first();

            $resultTotal = MemberMoneyLog::query()
                ->select(
                    DB::raw('SUM(money) as total_money')
                )
                ->where('operate_type', MemberMoneyLog::OPERATE_TYPE_FANSHUI)
                ->when($date, function ($query1) use ($date) {
                    $query1->whereBetween('updated_at', [$date, $date->clone()->endOfDay()]);
                })
                ->first();


            $dataLabels[] = $date->format('d/m/Y');
            $dataRecharge[] = $resultRecharge['total_money'];
            $dataDrawing[] = $resultDrawing['total_money'];
            $dataTotal[] = $resultTotal['total_money'];
        }

        return [
            'dataLabels' => $dataLabels,
            'dataRecharge' => $dataRecharge,
            'dataDrawing' => $dataDrawing,
            'dataTotal' => $dataTotal
        ];
    }

    public function getDataFromCountry(): array
    {
        $data = MemberLog::query()->groupBy('address')->pluck("address");
        $total = MemberLog::count();
        foreach ($data as $key => $value) {
            if ($this->isIpv6($value)) {
                unset($data[$key]);
            }
        }
        $countResults = [];
        foreach ($data as $key => $value) {
            $value = trim($value);
            $count = MemberLog::where('address', $value)->count();

            if (is_numeric($count)) {
                $countResults[$value] = $count;
            } else {
                $countResults[$value] = 0;
            }
        }
        $translatedArray = [];
        foreach ($countResults as $key => $count) {
            $translatedKey = __('res.' . trim($key));
            if (array_key_exists($translatedKey, $translatedArray)) {
                $translatedArray[$translatedKey] += $count;
            } else {
                $translatedArray[$translatedKey] = $count;
            }
        }
        $translatedArray["Khác"] = $total - array_sum($countResults);

        return [
            'dataLabels' => array_keys($translatedArray),
            'dataCountry' => array_values($translatedArray),
        ];
    }

    public function isIpv6($str)
    {
        return filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    public function getDataFromDevice(): array
    {
        $data = MemberLog::query()->groupBy('ua')->pluck("ua");
        $total = MemberLog::count();
        $userAgents = [];

        foreach ($data as $value) {
            $count = MemberLog::where('ua', $value)->count();
            $userAgents[$value] = $count;
        }
        $platformCounts = [];

        foreach ($userAgents as $userAgent => $count) {
            $platform = $this->parseUserAgent($userAgent);

            if ($platform !== null) {
                if (isset($platformCounts[$platform])) {
                    $platformCounts[$platform] += $count;
                } else {
                    $platformCounts[$platform] = $count;
                }
            }
        }

        $platformCounts["Khác"] = $total - array_sum($platformCounts);

        return [
            'dataLabels' => array_keys($platformCounts),
            'dataDevice' => array_values($platformCounts),
        ];
    }

    public function parseUserAgent($userAgent)
    {
        $platform = null;

        // Check for platforms
        if (preg_match('/Android ([\d\.]+)/', $userAgent, $matches)) {
            $platform = 'Android';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/Macintosh; Intel Mac OS X ([\d_]+)/', $userAgent, $matches)) {
            $platform = 'Mac OS X';
        } elseif (preg_match('/iPhone; CPU iPhone OS ([\d_]+)/', $userAgent, $matches)) {
            $platform = 'iPhone';
        } elseif (preg_match('/Windows NT ([\d\.]+)/', $userAgent, $matches)) {
            $platform = 'Windows';
        }

        return $platform;
    }
    public function iconlist(Request $request)
    {
        return view("layouts.icon_list");
    }

    public function picture_upload()
    {
        return view("admin.apigame.picture_uploader");
    }

    public function bankUpload()
    {
        return view("admin.bank.bank_uploader");
    }

    public function notice_undeal()
    {
        $now = Carbon::now();
        $data = [];

        $data['recharge'] = Recharge::where('status', Recharge::STATUS_UNDEAL)->count();
        $data['drawing'] = Drawing::where('status', Drawing::STATUS_UNDEAL)->count();
        $data['message'] = Message::where('status', Message::STATUS_NOT_DEAL)->where('send_type', Message::SEND_TYPE_MEMBER)->count();

        // 查询是否有需要提醒的用户登录
        /**
        $data['member'] = MemberLog::where('type',MemberLog::LOG_TYPE_API_LOGIN)
            ->whereIn('member_id',Member::where('is_tips_on',1)->pluck('id'))
            ->whereBetween('created_at',[Carbon::now()->subSeconds(15),$now])->count();
         */
        $data['member'] = MemberLog::memberRecent()
            ->whereIn('member_id', Member::where('is_tips_on', 1)->pluck('id'))->count();

        // 代理申请
        $data['agent_apply'] = MemberAgentApply::where('status', MemberAgentApply::STATUS_NOT_DEAL)->count();

        // 余额宝购买，金额日志表中 余额宝购买记录的备注表示已读时间
        $data['yuebao'] = MemberMoneyLog::where('operate_type', MemberMoneyLog::OPERATE_TYPE_FINANCIAL)
            ->where('model_name', get_class(app(MemberYuebaoPlan::class)))
            ->where('remark', '')
            ->whereBetween('created_at', [Carbon::now()->subDay(), $now])->count();

        // 活动申请提醒
        $data['activity'] = ActivityApply::where('status', ActivityApply::STATUS_NOT_DEAL)->count();

        // 借呗提醒
        $data['credit_apply'] = CreditPayRecord::where('type', CreditPayRecord::TYPE_BORROW)->where('status', CreditPayRecord::STATUS_UNDEAL)->count();

        app(ActivityService::class)->check_credit();
        $data['credit_overdue'] = CreditPayRecord::where('type', CreditPayRecord::TYPE_BORROW)
            ->where('is_return', 0)->where('is_overdue', 1)->where('is_read', 0)->count();

        $notices = '';
        // 循环data数组，取出需要提醒的 键值
        foreach ($data as $k => $v) {
            if ($v > 0) $notices .= $k . ',';
        }

        $data['notices'] = $notices;
        return $this->success(['data' => $data]);
    }


    // 游戏记录汇总
    public function gamerecord_total()
    {
        /**
        $api_types = DB::table('apis')->select('apis.api_name','apis.api_title','api_games.game_type')->leftJoin('api_games',function($join){
            $join->on('apis.api_name','=','api_games.api_name')->where('api_games.is_open',1);
        })->where('api_games.is_open',1)->groupBy('apis.api_name','apis.api_title','api_games.game_type')->get();

        $data = collect([]);

        foreach ($api_types as $key => $val){
            if(in_array($val->game_type,[1,2,3,6]) && !$data->where('api_name',$val->api_name)->where('fresh_time',300)->first()){
                $data->push([
                    'api_name' => $val->api_name,
                    'title' => $val->api_title.'(今天)',
                    'fresh_time' => 300,
                    'start_at' => $start_at,
                    'end_at' => $end_at
                ]);
            }

            else if(in_array($val->game_type,[4,99])){
                if(!$api_types->where('api_name',$val->api_name)->whereIn('game_type',[1,2,3,6])->first()){
                    $data->push([
                        'api_name' => $val->api_name,
                        'title' => $val->api_title.'(今天)',
                        'fresh_time' => 300,
                        'start_at' => $start_at,
                        'end_at' => $end_at
                    ]);
                }


                $data->push([
                    'api_name' => $val->api_name,
                    'title' => $val->api_title.'(昨天)',
                    'fresh_time' => 1800,
                    'start_at' => $yesterder_start_at,
                    'end_at' => $yesterder_end_at
                ]);
            }

            else if($val->game_type == 5){
                if(!$api_types->where('api_name',$val->api_name)->whereIn('game_type',[1,2,3,6])->first()){
                    $data->push([
                        'api_name' => $val->api_name,
                        'title' => $val->api_title.'(今天)',
                        'fresh_time' => 300,
                        'start_at' => $start_at,
                        'end_at' => $end_at
                    ]);
                }

                if(!$api_types->where('api_name',$val->api_name)->where('game_type',4)->first()){
                    $data->push([
                        'api_name' => $val->api_name,
                        'title' => $val->api_title.'(昨天)',
                        'fresh_time' => 1800,
                        'start_at' => $yesterder_start_at,
                        'end_at' => $yesterder_end_at
                    ]);
                }

                $data->push([
                    'api_name' => $val->api_name,
                    'title' => $val->api_title.'(前天)',
                    'fresh_time' => 3600,
                    'start_at' => $yesterder_start_at,
                    'end_at' => $yesterder_end_at
                ]);
            }
        }

        // dd($data->pluck('title'));
         */

        $start_at = date('Y-m-d H:i:s', time() - 3 * 3600);
        $end_at = date('Y-m-d H:i:s');

        $yesterder_start_at = date('Y-m-d H:i:s', time() - (3 + 24) * 3600);
        $yesterder_end_at = date('Y-m-d H:i:s', time() - 24 * 3600);

        $yesterder_before_start_at = date('Y-m-d H:i:s', time() - (3 + 24 * 2) * 3600);
        $yesterder_before_end_at = date('Y-m-d H:i:s', time() - 24 * 2 * 3600);

        $data = [
            [
                'api_name' => '',
                'title' => '全部 - 今天',
                'fresh_time' => 300,
                'start_at' => $start_at,
                'end_at' => $end_at
            ],
            [
                'api_name' => '',
                'title' => '全部 - 昨天',
                'fresh_time' => 1800,
                'start_at' => $yesterder_start_at,
                'end_at' => $yesterder_end_at
            ],
            [
                'api_name' => '',
                'title' => '全部 - 前天',
                'fresh_time' => 3600,
                'start_at' => $yesterder_before_start_at,
                'end_at' => $yesterder_before_end_at
            ],
        ];

        if (\App\Models\Api::where('api_name', \App\Models\Api::JZ_LOTTERY)->where('is_open', 1)->first()) {
            array_push($data, [
                'api_name' => 'JZ',
                'title' => '极致彩票 - 今天',
                'fresh_time' => 300,
                'start_at' => $start_at,
                'end_at' => $end_at
            ]);

            array_push($data, [
                'api_name' => 'JZ',
                'title' => '极致彩票 - 昨天',
                'fresh_time' => 1800,
                'start_at' => $yesterder_start_at,
                'end_at' => $yesterder_end_at
            ]);

            array_push($data, [
                'api_name' => 'JZ',
                'title' => '极致彩票 - 前天',
                'fresh_time' => 3600,
                'start_at' => $yesterder_before_start_at,
                'end_at' => $yesterder_before_end_at
            ]);
        }

        return view('admin.gamerecord.total', compact('data'));
    }

    // 游戏记录单个拉取
    public function gamerecord_pull()
    {
        return view('admin.gamerecord.pull');
    }

    // 定时发放代理的返点
    public function agent_fd_cron()
    {
        if (\systemconfig('agent_fd_mode'))
            return view('admin.gamerecord.fd');
        else
            exit('当前不是无限代理模式，不需要开启此页面');
    }

    // 补单操作
    // /gamerecord/check
    public function transfer_check()
    {
        return view('admin.gamerecord.bd');
    }

    // 修复数据库中失效的URL
    public function fix_url()
    {
        $oldurl = \systemconfig('site_domain', Base::LANG_COMMON);
        // $newurl = config('APP_URL');
        $newurl = env('APP_URL');

        if (!strlen($oldurl)) return $this->failed(trans('res.index.site_domain_required'));

        if (!$newurl) return $this->failed(trans('res.index.app_url_required'));

        if ($oldurl == $newurl) return $this->failed(trans('res.index.url_same_error'));

        try {
            // 创建 软连接
            app(MenuService::class)->checkUploadsFolder();

            // 批量替换所有的图片地址
            app(GameService::class)->replaceAllPic();
        } catch (\Exception $e) {
            return $this->failed($e->getMessage());
        }

        // 执行替换图片操作后，应该整个页面刷新
        // return $this->success([],'操作成功');
        return $this->success(['redirect' => route('admin.main')], trans('res.base.operate_success'));
    }
}
