<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Evaluation;

class EvaluationCompleted extends Notification implements ShouldQueue
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
        $evaluator = $this->evaluation->evaluator;
        
        return (new MailMessage)
            ->subject('Your Submission Has Been Evaluated')
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('Your submission for "' . $brief->title . '" has been evaluated by ' . $evaluator->username . '.')
            ->action('View Evaluation', route('student.evaluations.show', $this->evaluation->id))
            ->line('Thank you for participating in this project!');
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
        $evaluator = $this->evaluation->evaluator;
        
        return [
            'evaluation_id' => $this->evaluation->id,
            'brief_id' => $brief->id,
            'brief_title' => $brief->title,
            'evaluator_username' => $evaluator->username,
            'message' => 'Your submission for "' . $brief->title . '" has been evaluated by ' . $evaluator->username . '.',
            'completed_at' => $this->evaluation->completed_at,
        ];
    }
} 