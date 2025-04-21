<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Evaluation;
use Carbon\Carbon;

class EvaluationReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $evaluation;
    protected $daysRemaining;

    /**
     * Create a new notification instance.
     *
     * @param Evaluation $evaluation
     * @param int $daysRemaining
     * @return void
     */
    public function __construct(Evaluation $evaluation, $daysRemaining)
    {
        $this->evaluation = $evaluation;
        $this->daysRemaining = $daysRemaining;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $submitter = $this->evaluation->submission->user;
        $brief = $this->evaluation->submission->brief;
        $dueDate = $this->evaluation->due_date;
        
        $urgencyLevel = $this->daysRemaining <= 1 ? 'high' : 'medium';
        $urgencyText = $this->daysRemaining <= 1 ? 'Urgent: ' : '';
        
        $mailMessage = (new MailMessage)
            ->subject($urgencyText . 'Reminder: Evaluation Due Soon - ' . $brief->title)
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('This is a reminder that your evaluation is due soon.')
            ->line('**Brief**: ' . $brief->title)
            ->line('**Submission by**: ' . $submitter->username);
            
        if ($this->daysRemaining <= 0) {
            $mailMessage->line('**Status**: This evaluation is now overdue.');
        } else {
            $daysText = $this->daysRemaining == 1 ? '1 day' : $this->daysRemaining . ' days';
            $mailMessage->line('**Time Remaining**: ' . $daysText);
        }
        
        if ($dueDate) {
            $mailMessage->line('**Due Date**: ' . $dueDate->format('F j, Y'));
        }
        
        return $mailMessage
            ->action('Complete Evaluation', route('student.evaluations.edit', $this->evaluation->id))
            ->line('Please complete your evaluation as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $submitter = $this->evaluation->submission->user;
        $brief = $this->evaluation->submission->brief;
        
        $urgencyLevel = $this->daysRemaining <= 1 ? 'high' : 'medium';
        
        if ($this->daysRemaining <= 0) {
            $message = 'Your evaluation for "' . $brief->title . '" is now overdue.';
        } else {
            $daysText = $this->daysRemaining == 1 ? '1 day' : $this->daysRemaining . ' days';
            $message = 'Your evaluation for "' . $brief->title . '" is due in ' . $daysText . '.';
        }
        
        return [
            'evaluation_id' => $this->evaluation->id,
            'brief_id' => $brief->id,
            'brief_title' => $brief->title,
            'submitter_username' => $submitter->username,
            'due_date' => $this->evaluation->due_date ? $this->evaluation->due_date->format('Y-m-d') : null,
            'days_remaining' => $this->daysRemaining,
            'urgency' => $urgencyLevel,
            'message' => $message,
            'url' => route('student.evaluations.edit', $this->evaluation->id)
        ];
    }
} 