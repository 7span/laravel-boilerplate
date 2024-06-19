<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\VerifyUser as VerifyUserMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendOtpMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private object $user, private int|string $otp, private string $subject)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $data = [
                'otp' => $this->otp,
                'firstname' => $this->user->firstname,
                'lastname' => $this->user->lastname,
                'subject' => $this->subject,
            ];
            Mail::to($this->user->email)->send(new VerifyUserMail(data: $data));
        } catch (\Exception $e) {
            Log::error('Send OTP Error : ' . $e);
        }
    }
}
