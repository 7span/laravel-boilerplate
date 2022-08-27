<?php

namespace App\Jobs;

use Exception;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Notifications\ForgotPassword as NotificationsForgotPassword;

class ForgotPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $otp;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $otp)
    {
        $this->user = User::find($userId);
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Notification::route('mail', $this->user['email'])->notify(new NotificationsForgotPassword($this->user, $this->otp));
        } catch (Exception $error) {
            Log::info($error->getMessage());
        }
    }
}
