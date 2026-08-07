<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    private User $userObj;

    public function __construct()
    {
        $this->userObj = new User;
    }

    public function resource(int $id): User
    {
        return $this->userObj->findOrFail($id);
    }
}
