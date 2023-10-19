<?php

namespace App\Jobs;

use App\Mail\ForgetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ForgetPasswordMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private $user, private $otp)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = ['otp' => $this->otp, 'firstname' => $this->user->firstname, 'lastname' => $this->user->lastname];
        Mail::to($this->user->email)->send(new ForgetPassword($data));
    }
}
