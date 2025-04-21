<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evaluation;
use App\Notifications\EvaluationReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class SendEvaluationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'evaluations:reminders {--days=3 : Days before deadline to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to students about approaching evaluation deadlines';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $daysThreshold = $this->option('days');
        $today = Carbon::today();
        $targetDate = Carbon::today()->addDays($daysThreshold);
        
        $this->info("Sending reminders for evaluations due in {$daysThreshold} days (due on {$targetDate->format('Y-m-d')})...");
        
        // Get evaluations that are due in exactly the specified number of days
        $upcomingEvaluations = Evaluation::where('status', '!=', 'completed')
            ->whereDate('due_date', $targetDate)
            ->with(['evaluator', 'submission.brief', 'submission.user'])
            ->get();
            
        $this->info("Found {$upcomingEvaluations->count()} upcoming evaluations.");
        
        // Get overdue evaluations (send reminders every day)
        $overdueEvaluations = Evaluation::where('status', '!=', 'completed')
            ->whereDate('due_date', '<', $today)
            ->with(['evaluator', 'submission.brief', 'submission.user'])
            ->get();
            
        $this->info("Found {$overdueEvaluations->count()} overdue evaluations.");
        
        // Send reminders for upcoming evaluations
        foreach ($upcomingEvaluations as $evaluation) {
            $evaluator = $evaluation->evaluator;
            $daysRemaining = $daysThreshold;
            
            try {
                Notification::send($evaluator, new EvaluationReminder($evaluation, $daysRemaining));
                $this->info("Reminder sent to {$evaluator->username} for evaluation #{$evaluation->id}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder to {$evaluator->username}: {$e->getMessage()}");
            }
        }
        
        // Send reminders for overdue evaluations
        foreach ($overdueEvaluations as $evaluation) {
            $evaluator = $evaluation->evaluator;
            $dueDate = $evaluation->due_date;
            $daysOverdue = $today->diffInDays($dueDate, false); // negative value
            
            try {
                Notification::send($evaluator, new EvaluationReminder($evaluation, $daysOverdue));
                $this->info("Overdue reminder sent to {$evaluator->username} for evaluation #{$evaluation->id} ({$daysOverdue} days overdue)");
            } catch (\Exception $e) {
                $this->error("Failed to send overdue reminder to {$evaluator->username}: {$e->getMessage()}");
            }
        }
        
        $totalSent = $upcomingEvaluations->count() + $overdueEvaluations->count();
        $this->info("Sent {$totalSent} evaluation reminders.");
        
        return 0;
    }
} 