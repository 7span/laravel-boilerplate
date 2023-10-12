<?php

namespace App\Jobs;


use App\Mail\VerifyUser;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class VerifyUserMail implements ShouldQueue
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
        $data = ['otp' => $this->otp, 'firstname' => $this->user->firstname, 'lastname' => $this->user->lastname, 'subject' => __('email.verifyUserSubject')];
        Mail::to($this->user->email)->send(new VerifyUser($data));
    }
}
