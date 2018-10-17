<?php

namespace App\Console;

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
        \App\Console\Commands\VotesInRedisStoreToMysql::class,
        \App\Console\Commands\StorePublicVotesToRedis::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        //将redis中的投票信息存入mysql
        $schedule->command('VotesInRedisStoreToMysql')->everyTenMinutes();

        //把redis中所有候选人的大众票数遍历统计的总和存储再redis中
        $schedule->command('StorePublicVotesToRedis')->everyTenMinutes();

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
