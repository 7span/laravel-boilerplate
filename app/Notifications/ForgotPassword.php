<?php

namespace App\Notifications;

use App\Mail\ForgetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ForgotPassword extends Notification
{
    use Queueable;

    private $data;

    private $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($data, $user)
    {
        $this->data = $data;
        $this->user = $user;
    }

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
    public function toMail(object $notifiable)
    {
        return (new ForgetPassword($this->data))->to($this->user->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
