<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\UpdateProfile;
use Dedoc\Scramble\Attributes\QueryParameter;
use App\Http\Resources\User\Resource as UserResource;
use App\Http\Requests\User\ChangePassword as UserChangePassword;

/**
 * @tags Auth
 */
#[Group('Auth', weight: 10)]
class UserController extends Controller
{
    use ApiResponser;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

    /**
     * Profile.
     */
    #[QueryParameter('media')]
    public function me(): JsonResponse
    {
        $user = $this->userService->resource(Auth::id());

        return $this->resource(new UserResource($user));
    }

    /**
     * Update profile.
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
     * @response array{message: string}
     */
    public function changePassword(UserChangePassword $request): JsonResponse
    {
        $data = $this->userService->changePassword($request->validated());

        return $this->success($data, 200);
    }

    /**
     * Update locale.
     *
     * @response array{message: string}
     */
    public function updateLocale(Request $request): JsonResponse
    {
        $data = $this->userService->updateLocale($request->all());

        return $this->success($data, 200);
    }
}
