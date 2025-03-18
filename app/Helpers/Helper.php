<?php

namespace App\Helpers;

/**
 * Helper class containing utility methods.
 */
class Helper
{
    /**
     * Generate a random OTP of the specified length.
     *
     * @param int $length The length of the OTP.
     * @return string OTP as a string to preserve leading zeros.
     */
    public static function generateOTP(int $length): string
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException('OTP length must be greater than zero.');
        }

        return str_pad((string) mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }
}
