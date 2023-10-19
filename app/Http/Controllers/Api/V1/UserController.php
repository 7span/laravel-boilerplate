<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\UpdateProfile;
use App\Http\Resources\User\Resource as UserResource;

class UserController extends Controller
{
    use ApiResponser;

    public function __construct(private UserService $userService)
    {
        //
    }

    public function me()
    {
        $user = $this->userService->resource(Auth::id());

        return $this->resource(new UserResource($user));
    }

    public function updateProfile(UpdateProfile $request)
    {
        $data = $this->userService->update(Auth::id(), $request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
