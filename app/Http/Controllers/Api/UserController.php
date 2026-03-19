<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\UpdateProfile;
use App\Http\Resources\User\Resource as UserResource;
use App\Http\Requests\User\ChangePassword as UserChangePassword;

/**
 * @tags Auth
 */
#[Group('Auth', weight: 0)]
class UserController extends Controller
{
    use ApiResponser;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

    /**
     * Get authenticated user.
     *
     * Returns the profile details of the currently authenticated user.
     */
    public function me(): UserResource
    {
        $user = $this->userService->resource(Auth::id());

        return new UserResource($user);
    }

    /**
     * Update profile.
     *
     * Updates the profile information for the authenticated user.
     *
     * @response array{message: string, user: UserResource}
     */
    public function updateProfile(UpdateProfile $request): JsonResponse
    {
        $data = $this->userService->update(Auth::id(), $request->validated());

        return $this->success($data, 200);
    }

    /**
     * Change password.
     *
     * Changes the authenticated user's password after verifying the current one.
     *
     * @response array{message: string}
     */
    public function changePassword(UserChangePassword $request): JsonResponse
    {
        $data = $this->userService->changePassword($request->validated());

        return $this->success($data, 200);
    }
}
