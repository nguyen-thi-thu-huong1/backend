<?php

namespace App\Http\Controllers\Backend\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\MemberMoneyLog;
use Illuminate\Validation\Rule;

class MemberMoneyLogsController extends AdminBaseController
{
    protected $create_field = ['money','number_type','description','remark'];
    protected $update_field = ['money','number_type','description','remark'];

    public function __construct(MemberMoneyLog $model){
        $this->model = $model;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $beforeCreatedAt = null;
        if(isset($params['created_at'])) {
            $beforeCreatedAt = $params['created_at'];
            list($from, $to) = explode(' - ', $params['created_at']);
            $from = Carbon::createFromFormat('d/m/Y H:i:s', trim($from))->format('Y-m-d H:i:s');
            $to = Carbon::createFromFormat('d/m/Y H:i:s', trim($to))->format('Y-m-d H:i:s');
            $params['created_at'] = $from . ' - ' . $to;
        }

        $data = $this->model::with('member:id,name,is_in_on')
            ->memberName(isset_and_not_empty($params,'member_name',''))
            ->memberLang(isset_and_not_empty($params,'member_lang',''))
            ->where($this->convertWhere($params))
            ->latest()
            ->paginate(request('per_page', apiPaginate()));

        isset($params['created_at']) ? $params['created_at'] = $beforeCreatedAt : '';
        return view("{$this->view_folder}.index", compact('data', 'params'));
    }

    public function edit(MemberMoneyLog $MemberMoneyLog){
        return view($this->getEditViewName(),["model" => $MemberMoneyLog]);
    }

    public function storeRule(){
        return [
            "money" => "required|min:0",
			"money_type" => Rule::in(array_keys(config('platform.member_money_type'))),
			"number_type" => ["required",Rule::in(array_keys(config('platform.money_number_type')))],
			"operate_type" => Rule::in(array_keys(config('platform.member_money_operate_type'))),
		];
    }

    public function updateRule($id){
        return [
			"money" => "required|min:0",
			"money_type" => Rule::in(array_keys(config('platform.member_money_type'))),
			"number_type" => ["required",Rule::in(array_keys(config('platform.money_number_type')))],
			"operate_type" => Rule::in(array_keys(config('platform.member_money_operate_type'))),
		];
    }
}
