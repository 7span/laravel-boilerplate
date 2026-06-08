<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgetPasswordOtp extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @param string $otp */
    public function __construct(private ?User $user, private string $otp)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('email.forget_password.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $user = $this->user;
        if (!$user) {
            throw new \Exception('User not found');
        }
        return new Content(
            view: 'emails.forget-password-otp',
            with: ['user' => $user, 'otp' => $this->otp, 'name' => $user->name],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
