<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    private string $url;

    /**
     * Create a new message instance.
     */
    public function __construct(private ?array $data)
    {
        $this->url = url(
            config('site.front_website_url') .
                '/reset-password?token=' .
                $data['reset_password_token'] .
                '&email=' .
                $data['email']
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('email.forgetPasswordEmailSubject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.forget-password',
            with: [
                'data' => $this->data,
                'url' => $this->url,
            ],
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
