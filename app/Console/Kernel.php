<?php

namespace App\Console;

use DB;
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
        // $schedule->command('inspire')
        //          ->everyMinute()
        //          ->appendOutputTo(storage_path('logs/inspire.log'));

        // $schedule->call(function () {
        //     $kmrn = date('Y-m-d', strtotime('-1 day', strtotime(now())));
        //     DB::table('EWS_JADWAL_RKM')
        //         ->whereBetween('rkhDate', [$kmrn, $kmrn.' 23:59:59.000'])
        //         ->update(['rkhDate' => now()]);
        // })
        // ->daily();
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
