<?php

namespace App\Http\Controllers\Backend\Admin;

use Carbon\Carbon;
use App\Models\Member;
use App\Models\Drawing;
use App\Models\Recharge;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\MemberMoneyLog;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TransactionStatisticController extends Controller
{
    public function index(Request $request) {
        $params = $request->all();
        $confirmDate = [];
        $searchDate = [];

        $data = [];
        $total_recharges = $total_drawings = $total_fs = $total_dividend = $total_other = $total_yinli = 0;

        if(!count($params) || !$request->get('lang'))
            return view('admin.member.member_financial',compact('data','params','total_recharges','total_drawings',
                'total_fs','total_dividend','total_other','total_yinli'));
        $from = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[0]))->format('Y-m-d H:i:s') : '';
        $to = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[1]))->format('Y-m-d H:i:s') : '';
        $params['created_at'] = $from && $to ? $from . ' - ' . $to : '';
        // if(array_key_exists('created_at',$params) && $params['created_at']){
            // $confirmDate = convertDateToArray($params['created_at'],'confirm_at');
            // $searchDate = convertDateToArray($params['created_at'],'created_at');
            // dd($confirmDate, $searchDate);

        // }
        // ??????
        $mod = Member::where('status',Member::STATUS_ALLOW)
            ->where('lang',$request->get('lang'))
            ->filterInnerAccount()
            ->filterDemoAccount()
            ->has('recharges')
            ->with('recharges', 'drawings', 'moneylogs')
            ->when($request->get('name'),function($query) use ($request){
                $query->where('name','like','%'.$request->name.'%');
        })
            // rechargeSum ???????
            ->withCount(['recharges as rechargeSum' => function($query) use($params,$from, $to) {
                $query->whereBetween('confirm_at', [$from, $to])
                ->where('status',Recharge::STATUS_SUCCESS)
                ->select(DB::raw("sum(money) as member_recharge_sum"))
                ->where('money', '>', 0);
            }])
                // rechargeCount ?loca??????
                ->withCount(['recharges as rechargeCount' => function($query) use($params,$from, $to) {
                $query->whereBetween('confirm_at', [$from, $to])->where('status',Recharge::STATUS_SUCCESS);
            }])->having('rechargeCount', '>', 0)
            // drawingSum ???????
            ->withCount(['drawings as drawingSum' => function ($query) use ($from, $to) {
                $query->whereBetween('confirm_at', [$from, $to])->where('status',Drawing::STATUS_SUCCESS)->select(DB::raw("sum(money) as member_drawing_sum"))->where('money', '>', 0);
            }])
            // drawingCount ???????
            ->withCount(['drawings as drawingCount' => function($query) use ($from, $to) {
                $query->whereBetween('confirm_at', [$from, $to])->where('status',Drawing::STATUS_SUCCESS);
            }])->having('drawingCount', '>', 0)

            ->withCount(['moneylogs as moneylogSumFanshui' => function($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to])->where('operate_type',MemberMoneyLog::OPERATE_TYPE_FANSHUI)->select(DB::raw("sum(money) as member_fanshui_sum"));
            }])
            ->withCount(['moneylogs as moneylogSumHongli' => function($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to])->activityMoney()->select(DB::raw("sum(money) as member_hongli_sum"));
            }])
            ->withCount(['moneylogs as moneylogSumOther' => function($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to])->otherMoney()->select(DB::raw("sum(money) as member_other_sum"));
            }])
            ->withCount(['moneylogs as moneylogSumDebit' => function($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to])->DebitMoney()->select(DB::raw("sum(money) as member_debit_sum"));
            }]);
        $all_data = $mod->latest()->get();

        // ??
		$total_debit = $all_data->sum('moneylogSumDebit'); //??????
        $total_recharges = $all_data->sum('rechargeSum');
        $total_drawings = $all_data->sum('drawingSum');
        $total_fs = $all_data->sum('moneylogSumFanshui');
        $total_dividend = $all_data->sum('moneylogSumHongli');
        $total_other = $all_data->sum('moneylogSumOther') - $total_debit;

        $total_yinli = $total_recharges - $total_drawings;

        $data = $mod->paginate(request('per_page', apiPaginate()));
        $params['created_at'] = $from && $to ? Carbon::createFromFormat('Y-m-d H:i:s', trim(Str::of($params['created_at'])->explode(' - ')[0]))->format('d/m/Y H:i:s') . ' - ' . Carbon::createFromFormat('Y-m-d H:i:s', trim(Str::of($params['created_at'])->explode(' - ')[1]))->format('d/m/Y H:i:s') : '';

        return view('admin.member.member_financial',compact('data','params','total_recharges','total_drawings',
            'total_fs','total_dividend','total_other','total_yinli'));
    }
}
