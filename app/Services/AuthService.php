<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    private $userObj;

    public function __construct(User $userObj)
    {
        $this->userObj = $userObj;
    }

    public function signup($inputs)
    {
        $user = $this->userObj->create(Arr::only($inputs, ['name', 'email', 'password']));
        return $user;
    }

    public function login($inputs)
    {
        $user = $this->userObj->where('email', $inputs['email'])->first();

        if (!$user || !Hash::check($inputs['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        return $user;
    }
}
