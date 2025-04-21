<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Evaluation;

class EvaluationReminder extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The evaluation instance.
     *
     * @var \App\Models\Evaluation
     */
    protected $evaluation;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Evaluation  $evaluation
     * @return void
     */
    public function __construct(Evaluation $evaluation)
    {
        $this->evaluation = $evaluation;
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
        $brief = $this->evaluation->submission->brief;
        $dueDate = $this->evaluation->due_at ? $this->evaluation->due_at->format('F j, Y') : 'soon';
        $daysLeft = $this->evaluation->due_at ? $this->evaluation->due_at->diffInDays(now()) : null;
        
        $mailMessage = (new MailMessage)
            ->subject('Reminder: Evaluation Due ' . ($daysLeft ? "in $daysLeft days" : 'Soon'))
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('This is a reminder that you have an evaluation due for "' . $brief->title . '".');
        
        if ($daysLeft !== null) {
            $mailMessage->line('Your evaluation is due on ' . $dueDate . ' (' . $daysLeft . ' days remaining).');
        } else {
            $mailMessage->line('Please complete this evaluation as soon as possible.');
        }
        
        return $mailMessage
            ->action('Complete Evaluation', route('student.evaluations.edit', $this->evaluation->id))
            ->line('Thank you for your participation in the peer evaluation process!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $brief = $this->evaluation->submission->brief;
        $daysLeft = $this->evaluation->due_at ? $this->evaluation->due_at->diffInDays(now()) : null;
        
        return [
            'evaluation_id' => $this->evaluation->id,
            'brief_id' => $brief->id,
            'brief_title' => $brief->title,
            'message' => 'Reminder: You have an evaluation due ' . ($daysLeft ? "in $daysLeft days" : 'soon') . ' for "' . $brief->title . '".',
            'due_at' => $this->evaluation->due_at,
        ];
    }
} 