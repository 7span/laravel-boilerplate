<?php

namespace App\Services;

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
}
