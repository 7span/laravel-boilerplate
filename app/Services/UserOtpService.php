<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\UserOtp;
use Illuminate\Http\Resources\Json\JsonResource;

class UserOtpService
{
    private $userOtpObj;

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
        $userOtp = $this->userOtpObj->create($inputs);

        return $userOtp;
    }

    public function update(int $id, array $inputs): bool
    {
        $userOtp = $this->userOtpObj->where('id', $id)->update($inputs);

        return $userOtp;
    }

    public function isOtpExpired(int|string $createdAt, int|string|null $verifiedAt): string
    {
        $expirationTime = config('site.otpExpirationTimeInMinutes');

        $expirationDate = Carbon::parse($createdAt)->addMinutes($expirationTime)->format('Y-m-d H:i:s');

        return $verifiedAt !== null || date('Y-m-d h:i:s') > $expirationDate;
    }

    public function otpExists(int $userId, int|string $otp, int|string $otpFor): ?UserOtp
    {
        return $this->userOtpObj->whereUserId($userId)->whereOtp($otp)->where('otp_for', $otpFor)->first();
    }
}
