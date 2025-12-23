<?php

namespace App\Http\Controllers\Backend\Admin;

use Carbon\Carbon;
use App\Models\MemberLog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class MemberLogsController extends AdminBaseController
{
    protected $create_field = ['member_id','ip','address','ua','type','description','remark'];
    protected $update_field = ['member_id','ip','address','ua','type','description','remark'];

    public function __construct(MemberLog $model){
        $this->model = $model;
        parent::__construct();
    }

    public function index(Request $request) {
        $params = $request->all();
        $from = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[0]))->format('Y-m-d H:i:s') : '';
        $to = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[1]))->format('Y-m-d H:i:s') : '';
        $params['created_at'] = $from && $to ? $from . ' - ' . $to : '';
        $data = $this->model->where($this->convertWhere($params))->latest()->paginate(request('per_page', apiPaginate()));
        $params['created_at'] = $from && $to ? Carbon::createFromFormat('Y-m-d H:i:s', trim(Str::of($params['created_at'])->explode(' - ')[0]))->format('d/m/Y H:i:s') . ' - ' . Carbon::createFromFormat('Y-m-d H:i:s', trim(Str::of($params['created_at'])->explode(' - ')[1]))->format('d/m/Y H:i:s') : '';

        return view("{$this->view_folder}.index", compact('data', 'params'));
    }

    // public function edit(MemberLog $memberlog){
    //     return view($this->getEditViewName(),["model" => $memberlog]);
    // }

    // public function storeRule(){
    //     return [
	// 		"type" => Rule::in(array_keys(config('platform.member_log_type'))),
	// 	];
    // }

    // public function updateRule($id){
    //     return [
	// 		"type" => Rule::in(array_keys(config('platform.member_log_type'))),
	// 	];
    // }
}
