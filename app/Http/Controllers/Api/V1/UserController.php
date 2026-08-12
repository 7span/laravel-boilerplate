<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Traits\ApiResponser;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use App\Http\Requests\User\UpdateLocaleRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Resources\User\Resource as UserResource;

/**
 * @tags Auth
 */
#[Group('Auth', weight: 10)]
class UserController extends Controller
{
    use ApiResponser;

    public function __construct(private readonly UserService $userService) {}

    /**
     * My profile.
     *
     * @response UserResource
     */
    #[QueryParameter('media', description: 'Comma separated media tags to include, e.g. profile.')]
    public function me(): JsonResponse
    {
        $user = $this->userService->resource((int) auth()->id());

        return $this->resource(new UserResource($user));
    }

    /**
     * Update profile.
     *
     * @response array{message: string, user: UserResource}
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $data = $this->userService->update(auth()->id(), $request->validated());

        return $this->success($data);
    }

    /**
     * Change password.
     *
     * Revokes every other access token and keeps the current one active.
     *
     * @response array{message: string}
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $data = $this->userService->changePassword(auth()->user(), $request->validated());

        return $this->success($data);
    }

    /**
     * Update locale.
     *
     * @response array{message: string}
     */
    public function updateLocale(UpdateLocaleRequest $request): JsonResponse
    {
        $data = $this->userService->updateLocale(auth()->user(), $request->validated());

        return $this->success($data);
    }
}
