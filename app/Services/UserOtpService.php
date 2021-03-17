<?php

namespace App\Services;

use App\Models\UserOtp;

class UserOtpService
{
    private $userOtpObj;

    public function __construct(UserOtp $userOtpObj)
    {
        $this->userOtpObj = $userOtpObj;
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
