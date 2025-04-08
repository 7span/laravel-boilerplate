<?php

namespace App\Helpers;

class Helper
{
    public static function generateOTP(int $length): string
    {
        return (string) random_int(10 ** ($length - 1), (10 ** $length) - 1);
    }
}

    