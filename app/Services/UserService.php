<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Support\Facades\Auth;

class UserService
{
    private $userObj;

    private $userOtpObj;

    private $userOtpService;

    public function __construct(User $userObj)
    {
        $this->userObj = $userObj;
        $this->userOtpObj = new UserOtp();
        $this->userOtpService = new UserOtpService($this->userOtpObj);
    }

    public function resource($id, $inputs = null)
    {
        $user = $this->userObj->getQB()->findOrFail($id);

        return $user;
    }

    public function update($id, $inputs = null)
    {
        $userOtp = $this->userOtpObj->whereUserId(Auth::user()->id)->whereOtp($inputs['otp'])->where('otp_for', 'update_profile')->first();

        if ($userOtp == null) {
            $data = [
                'status' => false,
                'message' =>  __('message.invalidOtp')
            ];
            return $data;
        }

        $expirationTime = config('site.otpExpirationTimeInMinutes');
        $expirationDate = Carbon::parse($userOtp['created_at'])->addMinutes($expirationTime)->format('Y-m-d H:i:s');

        if ($userOtp['verified_at'] != null || date('Y-m-d h:i:s') > $expirationDate) {
            $data = [
                'status' => false,
                'message' => __('message.invalidOtp')
            ];

            return $data;
        }

        $this->userOtpService->update($userOtp['id'], ['verified_at' => date('Y-m-d h:i:s')]);

        $data = $this->resource($id);
        $data->update($inputs);

        $data = [
            'status' => true,
            'message' => __('message.userProfileUpdate')
        ];

        return $data;
    }
}
