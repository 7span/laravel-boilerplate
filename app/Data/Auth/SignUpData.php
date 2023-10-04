<?php

namespace App\Data\Auth;

use Spatie\LaravelData\Data;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Digits;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class SignUpData extends Data
{
    public function __construct(
        #[
            Email,
            Unique('users', 'email'),
            Max(255)
        ]
        public ?string $email,
        #[
            Password(min: 8),
            Confirmed
        ]
        public ?string $password,
        #[Max(20)]
        public ?string $firstname,
        #[Max(20)]
        public ?string $lastname,
        #[Max(20)]
        public string $username,
        #[Max(20)]
        public ?string $country_code,
        #[Max(10)]
        public ?string $mobile_number,
        #[Digits(6)]
        public ?int $otp
    ) {
    }

    public static function rules(ValidationContext $context): array
    {
        $rules = [];
        $user = Auth::user();

        if (Auth::check() == false) {
            $rules = [
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed'
            ];
        } else {
            if ($context->payload['email'] != $user->email) {
                $rules = [
                    'email' => 'required|email|unique:users,email,' . $user->id,
                    'otp' => 'required_with:email',
                ];
            } else {
                $rules = [
                    'email' => 'required|email|unique:users,email,' . $user->id,
                ];
            }
        }
        return $rules;
    }
}
