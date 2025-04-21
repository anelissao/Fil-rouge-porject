<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SendEvaluationReminders;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SendEvaluationReminders::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Send reminders 3 days before evaluation due date
        $schedule->command('evaluations:reminders --days=3')
                 ->dailyAt('09:00')
                 ->withoutOverlapping();
                 
        // Send reminders 1 day before evaluation due date
        $schedule->command('evaluations:reminders --days=1')
                 ->dailyAt('09:00')
                 ->withoutOverlapping();
                 
        // Send reminders for overdue evaluations
        $schedule->command('evaluations:reminders --days=0')
                 ->dailyAt('09:00')
                 ->withoutOverlapping();
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