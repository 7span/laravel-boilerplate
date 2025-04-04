<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\ForgetPasswordOtp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ForgetPasswordOtpMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private object $user, private int|string $otp)
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
                'name' => $this->user->name,
                'subject' => __('email.forgetPasswordEmailSubject'),
            ];
            Mail::to($this->user->email)->send(new ForgetPasswordOtp(data: $data));
        } catch (\Exception $e) {
            Log::error('Forgot Password Error : ' . $e);
        }
    }
}