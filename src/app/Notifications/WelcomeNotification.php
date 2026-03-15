<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private string $plainPassword, private string $fullName) {}

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
            ->subject(__('emails.welcome.subject'))
            ->greeting(__('emails.welcome.greeting', ['fullName' => $this->fullName]))
            ->line(__('emails.welcome.line_1'))
            ->line(__('emails.welcome.username_line', ['username' => $notifiable->username]))
            ->line(__('emails.welcome.password_line', ['password' => $this->plainPassword]))
            ->action(__('emails.welcome.action'), (string) route('login'))
            ->salutation(__('emails.welcome.salutation'));
    }
}
