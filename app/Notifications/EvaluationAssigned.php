<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Evaluation;

class EvaluationAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $evaluation;

    /**
     * Create a new notification instance.
     *
     * @param Evaluation $evaluation
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
        $submitter = $this->evaluation->submission->user;
        $brief = $this->evaluation->submission->brief;
        $dueDate = $this->evaluation->due_date;
        
        $mailMessage = (new MailMessage)
            ->subject('New Evaluation Assignment - ' . $brief->title)
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('You have been assigned to evaluate a submission for the brief: **' . $brief->title . '**')
            ->line('This submission was made by: ' . $submitter->username)
            ->action('Start Evaluation', route('student.evaluations.edit', $this->evaluation->id));
            
        if ($dueDate) {
            $mailMessage->line('**Deadline**: Please complete this evaluation by ' . $dueDate->format('F j, Y'));
        }
            
        return $mailMessage
            ->line('Thank you for participating in the peer review process!');
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
        
        return [
            'evaluation_id' => $this->evaluation->id,
            'brief_id' => $brief->id,
            'brief_title' => $brief->title,
            'submitter_username' => $submitter->username,
            'due_date' => $this->evaluation->due_date ? $this->evaluation->due_date->format('Y-m-d') : null,
            'message' => 'You have been assigned to evaluate a submission.',
            'url' => route('student.evaluations.edit', $this->evaluation->id)
        ];
    }
} 