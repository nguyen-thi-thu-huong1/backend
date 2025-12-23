<?php

namespace App\Http\Controllers\Backend\Admin;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AccountBalance;

class StatementController extends Controller
{
    public function index(Request $request){
        $params = $request->all();
        $from = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[0]))->format('Y-m-d H:i:s') : '';
        $to = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[1]))->format('Y-m-d H:i:s') : '';
        $params['created_at'] = $from && $to ? $from . ' - ' . $to : '';

        $data = AccountBalance::query()
            ->where('member_id', $params['member_id'])
            ->when($params['created_at'], function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            })
            ->orderBy('date', 'DESC')
            ->paginate(request('per_page', apiPaginate()));

        $params['created_at'] = $from && $to ? Carbon::createFromFormat('Y-m-d H:i:s', trim(Str::of($params['created_at'])->explode(' - ')[0]))->format('d/m/Y H:i:s') . ' - ' . Carbon::createFromFormat('Y-m-d H:i:s', trim(Str::of($params['created_at'])->explode(' - ')[1]))->format('d/m/Y H:i:s') : '';

        return view("admin.statements.index", compact('data'));

    }
}
