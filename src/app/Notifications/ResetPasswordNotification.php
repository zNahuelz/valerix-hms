<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);
        return (new MailMessage)
            ->subject(__('emails.recoveryEmail.subject'))
            ->greeting(__('emails.recoveryEmail.greeting', ['name' => $notifiable->name]))
            ->line(__('emails.recoveryEmail.line_1'))
            ->action(__('emails.recoveryEmail.action'), $url)
            ->salutation(__('emails.recoveryEmail.salutation'))
            ->line(__('emails.recoveryEmail.line_2'))
            ->line(__('emails.recoveryEmail.line_3'));
    }

}
