<?php

namespace App\Jobs;

use App\Mail\VerifyUser;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendOtpMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;

    private $otp;

    private $subject;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $otp, $subject)
    {
        $this->user = $user;
        $this->otp = $otp;
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = ['otp' => $this->otp, 'firstname' => $this->user->firstname, 'lastname' => $this->user->lastname, 'subject' => $this->subject];
        Mail::to($this->user->email)->send(new VerifyUser($data));
    }
}
