<?php

namespace App\Jobs;

use App\Mail\ForgetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
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
    public function __construct(private object $user)
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
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
                'reset_password_token' => $this->user->reset_password_token
            ];

            Mail::to($this->user->email)->send(new ForgetPassword(data: $data));
        } catch (\Exception $e) {
            Log::error('Forgot Password Error : ' . $e);
        }
    }
}
