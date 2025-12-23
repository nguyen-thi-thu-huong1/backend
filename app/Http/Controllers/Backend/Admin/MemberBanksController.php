<?php

namespace App\Http\Controllers\Backend\Admin;

use Carbon\Carbon;
use App\Models\MemberBank;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class MemberBanksController extends AdminBaseController
{
    protected $create_field = ['member_id','card_no','bank_type','phone','owner_name','bank_address','remark'];
    protected $update_field = ['card_no','bank_type','phone','owner_name','bank_address'];

    public function __construct(MemberBank $model){
        $this->model = $model;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $from = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[0]))->format('Y-m-d H:i:s') : '';
        $to = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[1]))->format('Y-m-d H:i:s') : '';
        $params['created_at'] = $from && $to ? $from . ' - ' . $to : '';
        $data = $this->model::with('member:id,name,is_in_on')
        ->memberName(isset_and_not_empty($params,'member_name',''))
        ->where($this->convertWhere($params))->latest()->paginate(request('per_page', apiPaginate()));
        $params['created_at'] = $from && $to ? Carbon::createFromFormat('Y-m-d H:i:s', trim(Str::of($params['created_at'])->explode(' - ')[0]))->format('d/m/Y H:i:s') . ' - ' . Carbon::createFromFormat('Y-m-d H:i:s', trim(Str::of($params['created_at'])->explode(' - ')[1]))->format('d/m/Y H:i:s') : '';

        return view("{$this->view_folder}.index", compact('data', 'params'));
    }



    public function edit(MemberBank $memberbank){
        return view($this->getEditViewName(),["model" => $memberbank]);
    }

    /*
    public function storeRule(){
        return [
			"card_no" => "required",
			"bank_type" => Rule::in(array_keys(config('platform.bank_type'))),
			"owner_name" => "required",
		];
    }
    */
    public function updateRule($id){
        return [
			"card_no" => "required",
			"bank_type" => Rule::in(array_keys(config('platform.bank_type'))),
			"owner_name" => "required",
		];
    }

}
