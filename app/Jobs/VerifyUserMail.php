<?php

namespace App\Jobs;

use App\Mail\VerifyUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerifyUserMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private $user, private $otp)
    {
        // Log::info($this->user);

        // $this->user = $user;
        // $this->otp = $otp;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info($this->user);

        $data = ['otp' => $this->otp, 'firstname' => $this->user->firstname, 'lastname' => $this->user->lastname, 'subject' => __('email.verifyUserSubject')];
        Mail::to($this->user->email)->send(new VerifyUser($data));
    }
}
