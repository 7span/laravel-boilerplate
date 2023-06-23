<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User\Resource as UserResource;

class UserController extends Controller
{
    use ApiResponser;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function me()
    {
        $user = $this->userService->resource(Auth::id());

        return $this->resource(new UserResource($user));
    }
}
