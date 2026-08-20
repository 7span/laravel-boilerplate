<?php

namespace App\Libraries;

use Illuminate\Support\Str;

class Helper
{
    public static function generateOTP(int $length): string
    {
        return Str::padLeft((string) random_int(0, 10 ** $length - 1), $length, '0');
    }
}
