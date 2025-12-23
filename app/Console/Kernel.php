<?php

namespace App\Console;

use App\Models\SystemConfig;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $config = SystemConfig::getConfigGroup('batch');

        $schedule->command('game:histories')->cron('*/' . data_get($config, 'game_histories_schedule') . ' * * * *');
        $schedule->command('sbo:resend-transaction')->cron('*/' . data_get($config, 'resend_transaction_schedule') . ' * * * *');
        $schedule->command('sbo:delete-transaction')->cron('0 0 1 * *');
        $schedule->command('get:account-balance')->dailyAt('00:00');
        $schedule->command('get:account-balance')->dailyAt('12:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
