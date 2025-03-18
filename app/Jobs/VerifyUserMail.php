<?php

namespace App\Jobs;

use App\Mail\VerifyUser;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Job to send a verification email to the user.
 */
class VerifyUserMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * User instance.
     *
     * @var object|null
     */
    private ?object $user;

    /**
     * One-Time Password (OTP).
     *
     * @var int|string
     */
    private int|string $otp;

    /**
     * Create a new job instance.
     *
     * @param object|null $user The user to whom the email is sent.
     * @param int|string $otp The OTP to be sent.
     */
    public function __construct(?object $user, int|string $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if (!$this->user || !isset($this->user->email)) {
            Log::warning('VerifyUserMail job aborted: User or email not provided.');
            return;
        }

        Log::info('Sending OTP email to user: ' . $this->user->first_name);

        try {
            $data = [
                'otp' => $this->otp,
                'first_name' => $this->user->first_name ?? '',
                'last_name' => $this->user->last_name ?? '',
                'subject' => __('email.verifyUserSubject'),
            ];

            Mail::to($this->user->email)->send(new VerifyUser($data));
        } catch (\Throwable $e) {
            Log::error('Error sending OTP email: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }
}
