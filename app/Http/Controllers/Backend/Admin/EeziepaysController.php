<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\EeziepayHistory;
use Illuminate\Http\Request;

class EeziepaysController extends AdminBaseController
{
    protected $create_field = ['billno', 'api_name', 'name', 'betAmount', 'validBetAmount', 'netAmount', 'gameType', 'flag', 'betTime'];
    protected $update_field = ['billno', 'api_name', 'name', 'betAmount', 'validBetAmount', 'netAmount', 'gameType', 'flag', 'betTime'];

    public function __construct()
    {
        parent::__construct();
        $this->model = app(EeziepayHistory::class);
    }

    public function index(Request $request)
    {
        $totalAmount = EeziepayHistory::sum('receive_amount');
        $params = $request->all();
        $query = EeziepayHistory::with('member');

        if (data_get($params, 'member_name')) {
            $query->whereHas('member', function ($q) use ($params) {
                $q->where('name', data_get($params, 'member_name'));
            });
        }

        if (data_get($params, 'bank_code')) {
            $query->where('bank_code', data_get($params, 'bank_code'));
        }

        if (data_get($params, 'status')) {
            $query->where('status', data_get($params, 'status'));
        }
        
        if (data_get($params, 'transaction_at')) {
            $date = data_get($params, 'transaction_at');
            $transaction_at = explode(" ~ ", $date);
            $query->whereBetween('transaction_at', $transaction_at);
        }

        $data = $query->orderBy(EeziepayHistory::getTableName() . '.id', 'desc')->paginate(request('per_page', apiPaginate()));

        return view('admin.eeziepays.index', compact('data', 'params', 'totalAmount'));
    }
}
