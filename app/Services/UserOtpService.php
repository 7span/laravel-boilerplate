<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\UserOtp;

class UserOtpService
{
    public function __construct(private UserOtp $userOtpObj)
    {
        //
    }

    public function resource($id, $inputs = null)
    {
        $userOtp = $this->userOtpObj->getQB()->where('id', $id)->first();

        return $userOtp;
    }

    public function store($inputs)
    {
        $userOtp = $this->userOtpObj->create($inputs);

        return $userOtp;
    }

    public function update($id, $inputs)
    {
        $userOtp = $this->userOtpObj->where('id', $id)->update($inputs);

        return $userOtp;
    }

    public function isOtpExpired($created_at, $verified_at)
    {
        $expirationTime = config('site.otpExpirationTimeInMinutes');
        $expirationDate = Carbon::parse($created_at)->addMinutes($expirationTime)->format('Y-m-d H:i:s');

        return ($verified_at !== null || date('Y-m-d h:i:s') > $expirationDate);
    }

    public function otpExists($userId, $otp, $otp_for)
    {
        return $this->userOtpObj->whereUserId($userId)->whereOtp($otp)->where('otp_for', $otp_for)->first();
    }
}
