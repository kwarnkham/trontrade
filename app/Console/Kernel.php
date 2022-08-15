<?php

namespace App\Console;

use App\Models\NetworkTransaction;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $schedule->call(function () {
            $updatedPurchaseRow = DB::table('purchases')->where('status', 5)->where('updated_at', '<=', now()->subDay())->update(['status' => 6]);
            if ($updatedPurchaseRow) Log::channel('schedule')->info("Schedule update rejected purchases. Updated rows " . $updatedPurchaseRow);
        })->everyFiveMinutes();

        $schedule->call(function () {
            $updatedPurchaseRow = DB::table('purchases')->where('status', 1)->where('updated_at', '<=', now()->subMinutes(30))->update(['status' => 4]);
            if ($updatedPurchaseRow) Log::channel('schedule')->info("Schedule update unpaid purchases. Updated rows " . $updatedPurchaseRow);
        })->everyMinute();

        $schedule->call(function () {
            $count = NetworkTransaction::sync();
            if ($count) Log::channel('schedule')->info("Schedule sync network transactions : " . $count);
        })->dailyAt('03:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
