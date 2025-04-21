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
        $dueDate = $this->evaluation->due_at ? $this->evaluation->due_at->format('F j, Y') : 'No deadline specified';
        
        return (new MailMessage)
            ->subject('New Evaluation Assignment')
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('You have been assigned to evaluate a submission for "' . $brief->title . '".')
            ->line('Due date: ' . $dueDate)
            ->action('Start Evaluation', route('student.evaluations.edit', $this->evaluation->id))
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
        
        return [
            'evaluation_id' => $this->evaluation->id,
            'brief_id' => $brief->id,
            'brief_title' => $brief->title,
            'message' => 'You have been assigned to evaluate a submission for "' . $brief->title . '".',
            'due_at' => $this->evaluation->due_at,
        ];
    }
} 