<?php

namespace App\Helpers;

class Helper
{
    public static function generateOTP($length)
    {
        $otp = mt_rand(pow(10, $length - 1), pow(10, $length) - 1);

        return $otp;
    }
}
