<?php

namespace App\Http\Controllers\Backend\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\Drawing;
use Illuminate\Support\Str;
use App\Models\SystemConfig;
use Illuminate\Http\Request;
use App\Models\MemberMoneyLog;
use Illuminate\Validation\Rule;
use App\Services\ActivityService;
use Illuminate\Support\Facades\DB;

class DrawingsController extends AdminBaseController
{
    protected $create_field = ['bill_no','member_id','name','money','account','before_money','after_money','score','counter_fee','fail_reason','member_bank_info','member_remark','confirm_at','status','user_id'];
    protected $update_field = ['bill_no','member_id','name','money','account','before_money','after_money','score','counter_fee','fail_reason','member_bank_info','member_remark','confirm_at','status','user_id'];

    public function __construct(Drawing $model){
        $this->model = $model;
        parent::__construct();
    }

    public function index(Request $request){
        $params = $request->all();
        $from = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[0]))->format('Y-m-d H:i:s') : '';
        $to = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[1]))->format('Y-m-d H:i:s') : '';
        $params['created_at'] = $from && $to ? $from . ' - ' . $to : '';

        $data = $this->model::with('member:id,name,lang')
        ->userName(isset_and_not_empty($params,'user_name',''))
        ->memberName(isset_and_not_empty($params,'member_name',''))
        ->memberLang(isset_and_not_empty($params,'member_lang',''))
        ->where($this->convertWhere($params))->latest()->paginate(request('per_page', apiPaginate()));
        $params['created_at'] = $from && $to ? Carbon::createFromFormat('Y-m-d H:i:s', trim(Str::of($params['created_at'])->explode(' - ')[0]))->format('d/m/Y H:i:s') . ' - ' . Carbon::createFromFormat('Y-m-d H:i:s', trim(Str::of($params['created_at'])->explode(' - ')[1]))->format('d/m/Y H:i:s') : '';

        return view("{$this->view_folder}.index", compact('data', 'params'));
    }

    public function confirm(Drawing $drawing,$status){
        return view($this->getEditViewName(),["model" => $drawing,'status' => $status]);
    }

    public function post_confirm(Drawing $drawing, Request $request){
        if($drawing->status != Drawing::STATUS_UNDEAL) return $this->failed(trans('res.drawing.msg.dealed_error'));

        // 通过会员的提款申请
        $data['status'] = Drawing::STATUS_SUCCESS;
        $data['user_id'] = $this->guard()->user()->id;
        $data['confirm_at'] = Carbon::now()->toDateTimeString();

        if($this->updateByModel($drawing,$data)){
            // 退还会员资金
            // update last money log or create new
            $log = MemberMoneyLog::where('member_id', $drawing->member_id)
                ->where('operate_type', MemberMoneyLog::OPERATE_TYPE_WITHDRAWAL_ACTIVITY)
                ->where('model_name', get_class($drawing))
                ->where('model_id', $drawing->id)
                ->first();

            if (empty($log)) {
                MemberMoneyLog::create([
                    'member_id' => $drawing->member_id,
                    'money' => $drawing->money,
                    'money_before' => $drawing->member->money,
                    'money_after' => $drawing->member->money + $drawing->money,
                    'number_type' => MemberMoneyLog::MONEY_TYPE_SUB,
                    'operate_type' => MemberMoneyLog::OPERATE_TYPE_MEMBER,
                    'user_id' => $data['user_id'],
                    'model_name' => get_class($drawing),
                    'model_id' => $drawing->id
                ]);
            } else {
                $log->money = $drawing->money;
                $log->money_before = $drawing->member->money;
                $log->money_after = $drawing->member->money + $drawing->money;
                $log->number_type = MemberMoneyLog::MONEY_TYPE_SUB;
                $log->operate_type = MemberMoneyLog::OPERATE_TYPE_MEMBER;
                $log->user_id = $data['user_id'];
                $log->save();
            }

			$bankInfo = json_decode($drawing->member_bank_info, true);
			$message = "[PHÊ DUYỆT RÚT TIỀN] - Người duyệt: ".$this->guard()->user()->name." - Trạng thái duyệt: thành công - Mã giao dịch: ".$drawing->bill_no." - Tài khoản rút: ".$drawing->name." - Số TK: ".$drawing->account." - Ngân hàng: ".$bankInfo['bank_type'];
			app(ActivityService::class)->sendAlertTelegram($message);
			return $this->success(['close_reload' => true], trans('res.base.operate_success'));
        }else{
            return $this->failed(trans('res.api.common.operate_again'));
        }
    }

    public function post_reject(Drawing $drawing, Request $request){
        $data = $request->only('fail_reason');

        $this->validateRequest($data,[
            'fail_reason' => 'required|min:1'
        ]);

        if($drawing->status != Drawing::STATUS_UNDEAL) return $this->failed(trans('res.drawing.msg.dealed_error'));

        $data['status'] = Drawing::STATUS_FAILED;
        $data['user_id'] = $this->guard()->user()->id;

        try{
            DB::transaction(function() use ($drawing, $data){
                $money = $drawing->money + $drawing->counter_fee;
                // 退还会员资金
                // update last money log or create new
                $log = MemberMoneyLog::where('member_id', $drawing->member_id)
                    ->where('operate_type', MemberMoneyLog::OPERATE_TYPE_WITHDRAWAL_ACTIVITY)
                    ->where('model_name', get_class($drawing))
                    ->where('model_id', $drawing->id)
                    ->first();

                if (empty($log)) {
                    MemberMoneyLog::create([
                        'member_id' => $drawing->member_id,
                        'money' => $money,
                        'money_before' => $drawing->member->money,
                        'money_after' => $drawing->member->money + $money,
                        'number_type' => MemberMoneyLog::MONEY_TYPE_SUB,
                        'operate_type' => MemberMoneyLog::OPERATE_TYPE_DRAWING_RETURN,
                        'user_id' => $data['user_id'],
                        'model_name' => get_class($drawing),
                        'model_id' => $drawing->id,
                        'description' => trans('res.member_money_log.notice.drawing_reject', ['money' => $money, 'reason' => $data['fail_reason']], $drawing->member->lang)
                    ]);
                } else {
                    $log->money = $money;
                    $log->money_before = $drawing->member->money;
                    $log->money_after = $drawing->member->money + $money;
                    $log->number_type = MemberMoneyLog::MONEY_TYPE_SUB;
                    $log->operate_type = MemberMoneyLog::OPERATE_TYPE_DRAWING_RETURN;
                    $log->user_id = $data['user_id'];
                    $log->description = trans('res.member_money_log.notice.drawing_reject', ['money' => $money, 'reason' => $data['fail_reason']], $drawing->member->lang);
                    $log->save();
                }

                $drawing->member->increment('money',$money);

                $drawing->update($data);
            });
        }catch(Exception $e){
            return $this->failed(trans('res.api.common.operate_again'));
        }
		$bankInfo = json_decode($drawing->member_bank_info, true);
		$message = "[PHÊ DUYỆT RÚT TIỀN] - Người duyệt: ".$this->guard()->user()->name." - Trạng thái duyệt: từ chối - Lý do từ chối: ".$data['fail_reason']." - Mã giao dịch: ".$drawing->bill_no." - Tài khoản rút: ".$drawing->name." - Số TK: ".$drawing->account." - Ngân hàng: ".$bankInfo['bank_type'];
		app(ActivityService::class)->sendAlertTelegram($message);

        return $this->success(['close_reload' => true], trans('res.base.operate_success'));
    }

    public function updateRule($id){
        return [
			"counter_fee" => "required",
			"status" => Rule::in(array_keys(config('platform.drawing_status'))),
		];
    }

    // 设置红包大小
    public function setting_size() {
        $data = systemconfig('drawing_money_size_json');
        if($data) {
            $data = json_decode($data,1);
        } else {
            $data = [];
            $lang_list = config('platform.currency_type');
            if ($lang_list) {
                foreach ($lang_list as $k_lang => $v_lang_name) {
                    $data[$k_lang]['a'] = $v_lang_name; // 最小，最大
                    $data[$k_lang]['b'] = [1, 1]; // 最小，最大
                }
            } else {
                $data = [];
            }
        }

        //var_dump($data);exit;

        return view('admin.drawing.setting_size',compact('data'));
    }

    public function post_setting_size(Request $request){
        $data = $request->all();
        //return $data['zh_cn'];
        $old_data = systemconfig('drawing_money_size_json');
        if($old_data) {
            $old_data = json_decode($old_data,true);
        } else {
            $old_data = [];
            $lang_list = config('platform.currency_type');
            if ($lang_list) {
                $arr = $lang_list;
                unset($arr['zh_hk']);
                foreach ($arr as $k_lang => $v_lang_name) {
                    $old_data[$k_lang]['a'] = $v_lang_name; // 最小，最大
                    $old_data[$k_lang]['b'] = [1, 1]; // 最小，最大
                }
            }
        }

        foreach ($old_data as $k_lang => $v) {
            if (isset($data[$k_lang])) {
                $old_data[$k_lang]['b'] = $data[$k_lang];
            }
        }

        $mod = SystemConfig::query()->getConfig('drawing_money_size_json');

        if($mod->update([
            'value' => json_encode($old_data, JSON_UNESCAPED_UNICODE)
        ])){
            return $this->success(['reload' => true],trans('res.base.save_success'));
        }else{
            return $this->failed(trans('res.base.save_fail'));
        }
    }
}
