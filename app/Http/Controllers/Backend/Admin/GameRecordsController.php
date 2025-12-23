<?php

namespace App\Http\Controllers\Backend\Admin;

use Carbon\Carbon;
use App\Models\Member;
use GuzzleHttp\Client;
use App\Models\GameRecord;
use Illuminate\Support\Str;
use App\Models\BetHistories;
use Illuminate\Http\Request;
use App\Exports\ReportExport;
use App\Services\MemberService;
use App\Models\TransactionHistory;
use App\Models\Wager;
use App\Services\GameRecordsService;
use Maatwebsite\Excel\Facades\Excel;

class GameRecordsController extends AdminBaseController
{
    public $gameRecordsService;

    protected $create_field = ['billno', 'api_name', 'name', 'betAmount', 'validBetAmount', 'netAmount', 'gameType', 'flag', 'betTime'];
    protected $update_field = ['billno', 'api_name', 'name', 'betAmount', 'validBetAmount', 'netAmount', 'gameType', 'flag', 'betTime'];

    public function __construct(GameRecord $model, GameRecordsService $gameRecordsService)
    {
        $this->model = $model;
        $this->gameRecordsService = $gameRecordsService;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $params = $request->all();
        $query = BetHistories::with('member', 'apiGame');

        if (request('member_name')) {
            $query->where('member_name', 'like', '%' . request('member_name') . '%');
        }

        if (request('member_id')) {
            $query->where('member_id', request('member_id'));
        }

        if (request('result_bet_status')) {
            $query->where('result_bet_status', request('result_bet_status'));
        }

        if (request('api_name')) {
            $query->where('api_name', request('api_name'));
        }

        if (request('bet_product')) {
            $query->where('bet_product', request('bet_product'));
        }

        $data = $query->orderBy(BetHistories::getTableName() . '.id', 'desc')->paginate(request('per_page', apiPaginate()));

        return view('admin.gamerecord.index', compact('data', 'params'));
    }

    public function destroy(Request $request, $id)
    {
        $id = $request->get("ids") ?? $id;

        if (BetHistories::whereIn('id', $id)->delete()) {
            return $this->success(["reload" => true], trans('res.base.delete_success'));
        }

        return $this->failed(trans('res.base.delete_fail'));
    }

    public function report(Request $request)
    {
        $userNameMember = $this->gameRecordsService->getDataReport($request->all())->pluck('name');
        $wagers = Wager::whereIn('member_name', $userNameMember)->get()->groupBy('member_name')->map(function ($group) {
            return $group->pluck('game_id')->unique();
        });
        $viewData = [
            'data' => $this->gameRecordsService->getDataReport($request->all()),
            'wagers' => $wagers,
            'params' => $request->all(),
        ];
        return view('admin.gamerecord.report', $viewData);
    }

    public function reportDetail(Request $request, $memberId)
    {
        $params = $request->all();
        $params['member_id'] = $memberId;
        // $from = data_get($params, 'created_at_from');
        // $to = data_get($params, 'created_at_to');
        $from = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[0]))->format('Y-m-d H:i:s') : null;
        $to = isset($params['created_at']) ? Carbon::createFromFormat('d/m/Y H:i:s', trim(Str::of($params['created_at'])->explode('-')[1]))->format('Y-m-d H:i:s') : null;
        $productType = data_get($params, 'product_type');
        $perPage = data_get($params, 'per_page', apiPaginate());
        $timezone = isset($params['timezone']) ? $params['timezone'] : 'created_at';
        $memberName = data_get(Member::query()->where('id', $memberId)->first(), 'name');
        $wagerList = Wager::where('member_name', $memberName)->get()->pluck('wager_id')->unique();
        $transactionHistoryModel = TransactionHistory::with('member', 'apiGame')
            ->where('member_id', $memberId)
            ->whereIn('status', [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST, TransactionHistory::STATUS_TIE, TransactionHistory::SWMD_STATUS])
            // filter by transaction_time
            ->when($from && $to, function ($query1) use ($from, $to, $timezone) {
                $query1->whereBetween($timezone, [$from, $to]);
            })

            // filter by product_type
            ->when($productType, function ($query) use ($productType) {
                $query->where('product_type', $productType);
            });

        $paginatedData = $transactionHistoryModel->orderBy(TransactionHistory::getTableName() . '.id', 'desc')->paginate($perPage);

        $wagerIds = $transactionHistoryModel->whereNotNull('wager_id')->pluck('wager_id');
        $dataSwmds = $this->pullReportByWagerIds($wagerIds);

        $paginatedData->getCollection()->transform(function ($item) use ($dataSwmds) {
            if ($item->wager_id && isset($dataSwmds[$item->wager_id])) {
                $item->currency_id = $swmdData['CurrencyID'] ?? null;
                $item->game_type = $swmdData['GameType'] ?? null;
                $item->game_id = $swmdData['GameID'] ?? null;
                $item->valid_bet_amount = $swmdData['ValidBetAmount'] ?? null;
                $item->bet_amount = $swmdData['BetAmount'] ?? null;
                $item->payout_amount = $swmdData['PayoutAmount'] ?? null;
                $item->commision_amount = $swmdData['CommisionAmount'] ?? null;
                $item->jackpot_amount = $swmdData['JackpotAmount'] ?? null;
                $item->jp_bet = $swmdData['JPBet'] ?? null;
                $item->settlement_date = $swmdData['SettlementDate'] ?? null;
                $item->status = $swmdData['Status'] ?? null;
            }
            return $item;
        });

        $viewData = [
            'data' => $paginatedData,
            'params' => $request->all(),
            'wagerList' => $wagerList,
            'id' => $memberId
        ];

        return view('admin.gamerecord.report_detail', $viewData);
    }

    public function betting(Request $request)
    {
        // get member list with histories
        $members = app(Member::class)->getBettingSboHistories($request->all());

        foreach ($members->items() as $member) {
            $histories = $member->sboRecords;
            $totalRecords = 0;
            $totalAmount = 0;

            foreach ($histories as $history) {
                if (!in_array($history->status, [TransactionHistory::STATUS_WAITING])) {
                    continue;
                }

                $totalRecords += 1;
                $totalAmount += $history->amount;
            }

            $member->total_records = $totalRecords;
            $member->total_amount = $totalAmount;
        }

        $viewData = [
            'data' => $members,
            'params' => $request->all(),
        ];

        return view('admin.gamerecord.betting', $viewData);
    }

    public function bettingDetail(Request $request, $memberId)
    {
        $params = $request->all();
        $params['member_id'] = $memberId;
        $from = data_get($params, 'created_at_from');
        $to = data_get($params, 'created_at_to');
        $productType = data_get($params, 'product_type');
        $perPage = data_get($params, 'per_page', apiPaginate());
        $timezone = isset($params['timezone']) ? $params['timezone'] : 'created_at';

        $transactionHistoryModel = TransactionHistory::with('member', 'apiGame')
            ->where('member_id', $memberId)
            ->whereIn('status', [TransactionHistory::STATUS_WAITING])
            // filter by transaction_time
            ->when($from && $to, function ($query1) use ($from, $to, $timezone) {
                $query1->whereBetween($timezone, [$from, $to]);
            })

            // filter by product_type
            ->when($productType, function ($query) use ($productType) {
                $query->where('product_type', $productType);
            });

        $viewData = [
            'data' => $transactionHistoryModel->orderBy(TransactionHistory::getTableName() . '.id', 'desc')->paginate($perPage),
            'params' => $request->all(),
        ];

        return view('admin.gamerecord.betting_detail', $viewData);
    }

    public function getBetDetail(Request $request, $id)
    {
        $response = app(Client::class)->request('POST', route('api.sbo.bet.detail'), [
            'json' => ['id' => $id]
        ]);
        $response = $response->getStatusCode() == 200 ? json_decode($response->getBody()->getContents(), true) : null;

        $viewData = [
            'url' => data_get($response, 'url'),
        ];

        return view('admin.gamerecord.get_bet_detail', $viewData);
    }

    public function getBetDetailSwmd($id)
    {
        $params = [
            'agentCode' => GameRecord::AGENT_CODE,
            'WagerID' => $id
        ];

        $viewData = [
            'url' =>  getConfig('sbo_swmd_api.bet_detail') . '?' . http_build_query($params),
        ];
        return view('admin.gamerecord.get_bet_detail', $viewData);
    }

    public function pullReportByWagerIds($ids)
    {
        $params = [
            'OperatorCode' => GameRecord::AGENT_CODE,
            'WagerIDs' => $ids
        ];

        $response = $this->gameRecordsService->pullReportByWagerIds($params);
        return $response['success'] ? $response['data'] : [];
    }

    public function exportSbo(Request $request, $id)
    {
        return Excel::download(new ReportExport($request, $id), 'report.xlsx');
    }
}
