<?php

namespace App\Services;

use App\Models\UserOtp;

class UserOtpService
{
    private UserOtp $userOtpObj;

    public function __construct()
    {
        $this->userOtpObj = new UserOtp;
    }
    
    public function resource(int $id, array $inputs = []): JsonResource
    {
        $userOtp = $this->userOtpObj->getQB()->where('id', $id)->first();

        return $userOtp;
    }

    public function store(array $inputs): object
    {
        return $this->userOtpObj->create($inputs);
    }

    public function update(int $id, array $inputs): bool
    {
        return (bool) $this->userOtpObj->where('id', $id)->update($inputs);
    }
    
    public function isOtpExpired(string $createdAt, ?string $verifiedAt): string
    {
        $expirationTime = config('site.otp_expiration_time_in_minutes');
        $expirationDate = Carbon::parse($createdAt)->addMinutes($expirationTime);

        return $verifiedAt !== null || now()->greaterThan($expirationDate);
    }

    public function otpExists(int $userId, int|string $otp, int|string $otpFor): ?UserOtp
    {
        return $this->userOtpObj->whereUserId($userId)
            ->whereOtp($otp)
            ->where('otp_for', $otpFor)
            ->first();
    }
}