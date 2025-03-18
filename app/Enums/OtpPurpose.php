<?php

namespace App\Enums;

/**
 * Enum representing the purposes for OTP (One-Time Password).
 */
enum OtpPurpose: string
{
    case VERIFICATION = 'verification';
    case UPDATE_PROFILE = 'update_profile';
    case RESET_PASSWORD = 'reset_password';

    /**
     * Get all OTP purposes as an array.
     *
     * @return array<string> An array of all OTP purpose values.
     */
    public static function all(): array
    {
        return array_map(
            fn (self $case): string => $case->value,
            self::cases()
        );
    }
}