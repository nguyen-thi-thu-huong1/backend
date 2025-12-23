<?php

namespace App\Providers;

use App\Models\Agent;
use App\Models\AgentFdRate;
use App\Models\Attachment;
use App\Models\DailyBonus;
use App\Models\Drawing;
use App\Models\InviteRate;
use App\Models\Member;
use App\Models\Message;
use App\Models\Permission;
use App\Models\Recharge;
use App\Models\User;
use App\Observers\AgentFdRateObserver;
use App\Observers\AgentObserver;
use App\Observers\AttachmentObserver;
use App\Observers\DailyBonusObserver;
use App\Observers\DrawingObserver;
use App\Observers\InviteRateObserver;
use App\Observers\MemberObserver;
use App\Observers\MessageObserver;
use App\Observers\PermissionObserver;
use App\Observers\RechargeObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Permission::observe(PermissionObserver::class);
        Attachment::observe(AttachmentObserver::class);
        Member::observe(MemberObserver::class);
        Agent::observe(AgentObserver::class);
        Message::observe(MessageObserver::class);
        DailyBonus::observe(DailyBonusObserver::class);
        AgentFdRate::observe(AgentFdRateObserver::class);
        Recharge::observe(RechargeObserver::class);
        Drawing::observe(DrawingObserver::class);
        InviteRate::observe(InviteRateObserver::class);

        // log sql
        $this->logSql();
    }

    protected function logSql()
    {
        if (!isLogSql()) {
            return;
        }

        try {
            DB::listen(function ($sql) {
                $isJobs = strpos($sql->sql, 'jobs') !== false || strpos($sql->sql, 'failed_jobs') !== false;
                if (App::runningInConsole() && $isJobs) {
                    return;
                }

                foreach ($sql->bindings as $i => $binding) {
                    if ($binding instanceof \DateTime) {
                        $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                    } else {
                        if (is_string($binding)) {
                            $sql->bindings[$i] = "'$binding'";
                        }
                    }
                }
                // Insert bindings into query
                $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);
                $query = vsprintf($query, $sql->bindings);
                $log = "Time: {$sql->time} - SQL: {$query}";

                Log::debug($log, [], 'NASUCTRH', 'sql_log');
            });
        } catch (\Exception $exception) {
            // write log errors
        }
    }
}
