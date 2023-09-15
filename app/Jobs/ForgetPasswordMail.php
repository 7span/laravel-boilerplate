<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Notifications\ForgotPassword;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ForgetPasswordMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;

    private $otp;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = ['otp' => $this->otp, 'name' => $this->user->name];
        $this->user->notify(new ForgotPassword($data, $this->user));
    }
}
