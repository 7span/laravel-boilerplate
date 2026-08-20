<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeUser extends Notification implements ShouldQueue
{
    use Queueable;

    /** @return array<int, string> */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('email.welcome_user.subject', ['app_name' => __('email.app.name')]))
            ->view('emails.welcome-user', ['name' => $notifiable->name]);
    }
}
