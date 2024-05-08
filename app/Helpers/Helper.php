<?php

namespace App\Helpers;

class Helper
{
    public static function generateOTP(int $length): int|string
    {
        $otp = mt_rand(pow(10, $length - 1), pow(10, $length) - 1);

        return $otp;
    }
}
