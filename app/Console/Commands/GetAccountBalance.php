<?php

namespace App\Console\Commands;

use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use App\Models\TransactionHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\GameRecordsService;

class GetAccountBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:account-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get account balance current';
    protected $gameRecordsService;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GameRecordsService $gameRecordsService)
    {
        $this->gameRecordsService = $gameRecordsService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info('--------------------------------BEGIN GET ACCOUNT BALANCE--------------------------------' . now());
        $now = now();
        $hour = (int) $now->format('H');
        $data = [];
        // get member account balance
        $members = $this->gameRecordsService->getDataReport(request()->all());
        foreach($members as $member){
            $updateData = $this->dataUpdate($member);

            $row = array_merge([
                'member_id' => $member->id,
                'date' => now()->format('Y-m-d'),
            ], $updateData);
            $data[] = $row;
        }

        if($hour < 12){
            $exists = DB::table('account_balances')
                ->whereDate('date', now()->toDateString())
                ->exists();
            if (!$exists) {
                DB::table('account_balances')->insert($data);
            }
        }else{
            $existingMemberIds = DB::table('account_balances')
                ->whereDate('date', now()->toDateString())
                ->whereIn('member_id', array_column($data, 'member_id'))
                ->pluck('member_id')
                ->toArray();

            $toInsert = [];
            $toUpdate = [];

            foreach ($data as $item) {
                if (in_array($item['member_id'], $existingMemberIds)) {
                    $toUpdate[] = $item;
                } else {
                    $toInsert[] = $item;
                }
            }
            if (!empty($toInsert)) {
                DB::table('account_balances')->insert($toInsert);
            }

            foreach ($toUpdate as $item) {
                DB::table('account_balances')
                    ->where('member_id', $item['member_id'])
                    ->where('date', now()->toDateString())
                    ->update(Arr::except($item, ['member_id', 'date', 'created_at']));
            }
        }
        Log::info('--------------------------------END GET ACCOUNT BALANCE--------------------------------' . now());
        return 0;
    }

    private function dataUpdate($member){
        return [
                'balance_start_day' => isset($member->money) ? moneyFormat($member->money) : 0,
                'balance_middle_day' => isset($member->money) ? moneyFormat($member->money) : 0,
                'bet_amount' => isset($member->total_amount) ? moneyFormat($member->total_amount) : 0,
                'canceled_amount' => isset($member->sboRecords) ? $member->sboRecords->where('status', TransactionHistory::STATUS_CANCEL)->sum('amount') : 0,
                'pending_amount' => isset($member->sboRecords) ? $member->sboRecords->where('status', TransactionHistory::IS_FS_OFF)->sum('amount') : 0,
                'win_loss' => isset($member->total_amount_win) && isset($member->total_amount_loss) ? moneyFormat($member->total_amount_win - $member->total_amount_loss) : 0,
                'commission' => isset($member->total_fs_money) ? moneyFormat($member->total_fs_money) : 0,
                'created_at' => now(),
                'updated_at' => now(),
        ];
    }
}