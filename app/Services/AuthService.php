<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\Helper;
use App\Models\UserOtp;
use App\Mail\VerifyUser;
use App\Services\UserOtpService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    private User $userObj;
    private UserOtp $userOtpObj;
    private UserOtpService $userOtpService;

    public function __construct()
    {
        $this->userObj = new User;
        $this->userOtpObj = new UserOtp;
        $this->userOtpService = new UserOtpService;
    }
    
    public function signup(array $inputs): array
    {
        $user = $this->userObj->create($inputs);
        $otp = Helper::generateOTP(config('site.generate_otp_length'));
        $this->userOtpService->store(['otp' => $otp, 'user_id' => $user->id, 'otp_for' => 'verification']);

        try {
            Mail::to($user->email)->queue(new VerifyUser($user->toArray(), $otp));
        } catch (\Exception $e) {
            Log::error('User verification mail failed: ' . $e->getMessage());
        }

        $data = [
            'message' => __('message.userSignUpSuccess'),
            'data' => $user,
            'token' => $user->createToken(config('app.name'))->plainTextToken,
        ];

        return $data;
    }
}