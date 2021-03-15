<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\Resource as UserResource;
use App\Http\Resources\User\Collection as UserCollection;

class UserController extends Controller
{
    use ApiResponser;
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function me(Request $request)
    {
        $user = $this->userService->resource(Auth::id(), $request->all());
        return $this->resource(new UserResource($user));
    }
}
