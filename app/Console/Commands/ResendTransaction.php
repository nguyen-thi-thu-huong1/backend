<?php

namespace App\Console\Commands;

use App\Models\Base;
use App\Models\SystemConfig;
use App\Models\TransactionHistory;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResendTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sbo:resend-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SBO resend transaction';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('--------------------------------BEGIN RESEND TRANSACTION--------------------------------');

        // get system config
        $systemConfigs = app(SystemConfig::class)
            ->where('config_group', 'remote_api')
            ->where('lang', Base::LANG_COMMON)
            ->get()
            ->pluck('value', 'name')
            ->toArray();

        // get error histories
        $histories = TransactionHistory::where('status', TransactionHistory::STATUS_WAITING)->get();

        $params = [
            'CompanyKey' => data_get($systemConfigs, 'company_key'),
            'ServerId' => data_get($systemConfigs, 'server_id'),
        ];

        $txnId = [];

        try {
            foreach ($histories as $history) {
                $txnId[] = $history->transfer_code;
                $params['TxnId'] = $history->transfer_code;
                $params['Portfolio'] = $history->getProductTypeText();

                app(Client::class)->request('POST', getConfig('sbo_api.resend_order'), ['json' => $params]);
            }

            if (!empty($txnId)) {
                Log::info('SUCCESS: Resend transaction success: ' . implode(',', $txnId));
            }
        } catch (\Exception $e) {
            Log::error($e);
            Log::info('ERROR: Resend transaction error: ' . implode(',', $txnId));
        }

        Log::info('--------------------------------END RESEND TRANSACTION--------------------------------');
    }
}
