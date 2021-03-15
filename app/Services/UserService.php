<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    private $userObj;
    public function __construct(User $userObj)
    {
        $this->userObj = $userObj;
    }

    public function resource($id, $inputs = null)
    {
        $user = $this->userObj->getQB()->findOrFail($id);
        return $user;
    }
}
