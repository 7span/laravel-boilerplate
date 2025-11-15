<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use App\Services\UserService;
use OpenApi\Attributes as OA;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\UpdateProfile;
use App\Http\Resources\User\Resource as UserResource;
use App\Http\Requests\User\ChangePassword as UserChangePassword;
use App\Models\User;
use App\OpenApi\Attributes\ApiModel;

class UserController extends Controller
{
    use ApiResponser;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

    #[ApiModel(User::class)]
    #[OA\Get(
        path: '/api/me',
        tags: ['Auth'],
        summary: 'Get logged-in user details',
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function me(): JsonResponse
    {
        $user = $this->userService->resource(Auth::id());

        return $this->resource(new UserResource($user));
    }

    #[OA\Get(
        path: '/api/me/stats',
        tags: ['Auth'],
        summary: 'Get logged-in user statistics',
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function stats(): JsonResponse
    {
        $stats = $this->userService->getUserStats(Auth::id());

        return $this->success($stats);
    }

    #[OA\Post(
        path: '/api/me',
        operationId: 'updateProfile',
        tags: ['Auth'],
        summary: 'Update Profile',
        parameters: [
            new OA\Parameter(
                name: 'X-Requested-With',
                in: 'header',
                required: true,
                description: 'Custom header for XMLHttpRequest',
                schema: new OA\Schema(
                    type: 'string',
                    default: 'XMLHttpRequest'
                )
            ),
        ],
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function updateProfile(UpdateProfile $request): JsonResponse
    {
        $data = $this->userService->update(Auth::id(), $request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/change-password',
        operationId: 'changePassword',
        tags: ['Auth'],
        summary: 'Change Password',
        description: "Changes the user's password by verifying the current password and setting a new one.",
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function changePassword(UserChangePassword $request): JsonResponse
    {
        $data = $this->userService->changePassword($request->validated());

        return $this->success($data, 200);
    }
}
