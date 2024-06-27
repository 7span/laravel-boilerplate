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

class VerifyUserMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private ?object $user, private int|string $otp) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info($this->user);

        try {
            $data = [
                'otp' => $this->otp,
                'firstname' => $this->user->firstname,
                'lastname' => $this->user->lastname,
                'subject' => __('email.verifyUserSubject'),
            ];
            Mail::to($this->user->email)->send(new VerifyUser($data));
        } catch (\Exception $e) {
            Log::error('Send OTP Error : ' . $e);
        }
    }
}
