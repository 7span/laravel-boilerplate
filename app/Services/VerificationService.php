<?php

namespace App\Services;

use App\Models\User;

class VerificationService
{
    public function __construct(private User $userObj)
    {
        //
    }

    public function verify($user, $inputs = null)
    {
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        $data = [
            'message' => __('message.emailVerifySuccess'),
        ];

        return $data;
    }
}
