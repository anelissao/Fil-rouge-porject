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
        $evaluator = $this->evaluation->evaluator;
        $brief = $this->evaluation->submission->brief;
        
        return (new MailMessage)
            ->subject('Evaluation Received - ' . $brief->title)
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('Your submission for the brief: **' . $brief->title . '** has been evaluated.')
            ->line('The evaluation was completed by: ' . $evaluator->username)
            ->action('View Evaluation Results', route('student.evaluations.show', $this->evaluation->id))
            ->line('Take some time to review the feedback to improve your future work.')
            ->line('You can also provide feedback on the quality of this evaluation.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $evaluator = $this->evaluation->evaluator;
        $brief = $this->evaluation->submission->brief;
        
        return [
            'evaluation_id' => $this->evaluation->id,
            'brief_id' => $brief->id,
            'brief_title' => $brief->title,
            'evaluator_username' => $evaluator->username,
            'completed_at' => $this->evaluation->completed_at->format('Y-m-d H:i:s'),
            'message' => 'Your submission has been evaluated.',
            'url' => route('student.evaluations.show', $this->evaluation->id)
        ];
    }
} 