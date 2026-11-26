<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ForgotPasswordOtp extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $otp) {}

    /** @return array<int, string> */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('email.forgot_password.subject'))
            ->view('emails.forgot-password-otp', [
                'name' => $notifiable->name,
                'otp' => $this->otp,
            ]);
    }
}
