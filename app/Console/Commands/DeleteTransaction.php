<?php

namespace App\Console\Commands;

use App\Models\SystemConfig;
use App\Models\TransactionHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sbo:delete-transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete transaction histories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('--------------------------------BEGIN DELETE TRANSACTION HISTORIES--------------------------------');

        try {
            $config = SystemConfig::getConfigGroup('batch');

            TransactionHistory::where('transaction_time', '<', Carbon::now()->subDays(data_get($config, 'transaction_histories_keep_day')))->forceDelete();

            Log::info('SUCCESS');
        } catch (\Exception $e) {
            Log::error('ERROR', [$e]);
        }

        Log::info('--------------------------------END DELETE TRANSACTION HISTORIES--------------------------------');
    }
}
