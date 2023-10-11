<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function __construct(private User $userObj)
    {
        //
    }

    public function resource($id, $inputs = null)
    {
        $user = $this->userObj->getQB()->findOrFail($id);

        return $user;
    }
}
