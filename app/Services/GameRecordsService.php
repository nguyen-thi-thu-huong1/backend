<?php

namespace App\Services;

use App\Models\Member;
use GuzzleHttp\Client;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\Log;

class GameRecordsService
{
    private $memberService;
    protected $client;

    public function __construct(Client $client)
    {
        $this->memberService = app(MemberService::class);
        $this->client = $client;
    }

    public function getDataReport($data)
    {
        // get member list with histories
        $members = app(Member::class)->getSboHistories($data);
        foreach ($members->items() as $member) {
            $histories = $member->sboRecords;
            $totalRecords = 0;
            $totalWin = 0;
            $totalLoss = 0;
            $totalAmount = 0;
            $totalAmountWin = 0;
            $totalAmountLoss = 0;
            $totalFsSbo = 0;

            // fs refund
            $fsInfo = $this->memberService->getFsSbo($member, ['is_fs_all' => true]);
            $fsSbo = data_get($fsInfo, 'data');

            $fsInfo = $this->memberService->getFsSboSaba($member, ['is_fs_all' => true]);
            $fsSboSaba = data_get($fsInfo, 'data');

            $fsInfo = $this->memberService->getFsSboAfb($member, ['is_fs_all' => true]);
            $fsSboAfb = data_get($fsInfo, 'data');

            $fsInfo = $this->memberService->getFsSboBti($member, ['is_fs_all' => true]);
            $fsSboBti = data_get($fsInfo, 'data');

            $fsSbo->map(function ($item) use (&$totalFsSbo) {
                $totalFsSbo += $item->fs_money;
            });

            $fsSboSaba->map(function ($item) use (&$totalFsSbo) {
                $totalFsSbo += $item->fs_money;
            });

            $fsSboAfb->map(function ($item) use (&$totalFsSbo) {
                $totalFsSbo += $item->fs_money;
            });

            $fsSboBti->map(function ($item) use (&$totalFsSbo) {
                $totalFsSbo += $item->fs_money;
            });

            foreach ($histories as $history) {
                // if (!in_array($history->status, [TransactionHistory::STATUS_WIN, TransactionHistory::STATUS_LOST, TransactionHistory::STATUS_TIE])) {
                //     continue;
                // }

                if ($history->status == TransactionHistory::STATUS_WIN) {
                    $totalWin += 1;
                    $totalAmountWin += $history->win_loss - $history->amount;
                }

                if ($history->status == TransactionHistory::STATUS_LOST) {
                    $totalLoss += 1;
                    $totalAmountLoss += -$history->win_loss + $history->amount;
                }

                $totalRecords += 1;
                $totalAmount += $history->amount;
            }

            $member->total_records = $totalRecords;
            $member->total_win = $totalWin;
            $member->total_loss = $totalLoss;

            $member->total_amount = $totalAmount;
            $member->total_amount_win = $totalAmountWin;
            $member->total_amount_loss = $totalAmountLoss;

            $member->total_fs_money = $totalFsSbo;
        }
        return $members;
    }

    public function pullReportByWagerIds($data)
    {
        try {
            $url = getConfig('sbo_swmd_api.pull_report_wager_ids');

            $response = $this->client->post($url, [
                'json' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);


            // Kiểm tra nếu request thành công (status code 2xx)
            if ($responseData) {
                // Kiểm tra cấu trúc response
                if (isset($responseData['ErrorCode'])) {
                    // Xử lý dữ liệu thành công
                    $wagers = $responseData['Wagers'];

                    return [
                        'success' => true,
                        'data' => $wagers
                    ];
                } else {
                    throw new \Exception('Cấu trúc response không hợp lệ');
                }
            }
        } catch (\Exception $e) {
            // Log lỗi
            Log::error('API Request Failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => $e->getCode() ?: 500
            ];
        }
    }
}
