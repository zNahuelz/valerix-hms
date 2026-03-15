<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private string $fullDate){}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('emails.password_updated.subject'))
            ->greeting(__('emails.password_updated.greeting', ['username' => $notifiable->username]))
            ->line(__('emails.password_updated.line_1', ['fullDate' => $this->fullDate]))
            ->line(__('emails.password_updated.line_2'))
            ->line(__('emails.password_updated.line_3'))
            ->salutation(__('emails.password_updated.salutation'));
    }
}
