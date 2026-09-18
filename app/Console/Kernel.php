<?php

namespace App\Console;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\MessageQueue;
use App\Jobs\SendMessageJob;
use App\Http\Controllers\MultipleCronController;
use App\Models\AttendanceSetting;
use Illuminate\Http\Request;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
     protected $commands = [
        Commands\DemoCron::class
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
        $pendingMessages = MessageQueue::where('message_status', 0)->get();
        Log::info("✅ TestJob is running!");

        foreach ($pendingMessages as $message) {
          
            SendMessageJob::dispatch($message);
        }
    })->everyMinute();

    $schedule->call(function () {
        $settings = AttendanceSetting::where('attendance_type', 1)
            ->get(['branch_id', 'session_id']);
        $controller = app(MultipleCronController::class);

        foreach ($settings as $setting) {
            $request = Request::create('/attendance/attendance-cron-manager', 'GET', [
                'branch_id' => (int) $setting->branch_id,
                'session_id' => (int) $setting->session_id,
            ]);
            $controller->detectAttendanceType($request);
        }
    })->everyMinute()->withoutOverlapping();
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
