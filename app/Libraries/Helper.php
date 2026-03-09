<?php

namespace App\Libraries;

use Spatie\LaravelData\Optional;
use Illuminate\Database\Eloquent\Model;

class Helper
{
    public static function generateOTP(int $length): int|string
    {
        $otp = mt_rand(pow(10, $length - 1), pow(10, $length) - 1);

        return $otp;
    }

    public static function getRequestedAppends(Model $model, string $attribute): string|Optional
    {
        $requestedAppends = $model->getAppends();

        return in_array($attribute, $requestedAppends, true) ? $model->getAttribute($attribute) : Optional::create();
    }
}
