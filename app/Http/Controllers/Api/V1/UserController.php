<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\UserService;
use OpenApi\Attributes as OA;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\UpdateProfile;
use App\Http\Resources\User\Resource as UserResource;
use App\Http\Requests\User\ChangePassword as UserChangePassword;

class UserController extends Controller
{
    use ApiResponser;

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService;
    }

    #[OA\Get(
        path: '/api/v1/me',
        tags: ['Auth'],
        summary: 'Get logged-in user details',
        x: ['model' => User::class],
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function me(): JsonResponse
    {
        $user = $this->userService->resource(Auth::id());

        return $this->resource(new UserResource($user));
    }

    #[OA\Post(
        path: '/api/v1/me',
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
        path: '/api/v1/change-password',
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

    #[OA\Post(
        path: '/api/v1/locale',
        operationId: 'updateLocale',
        tags: ['Auth'],
        summary: 'Update Locale',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'locale', type: 'string', format: 'max:5'),
                ]
            )
        ),
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function updateLocale(Request $request): JsonResponse
    {
        $data = $this->userService->updateLocale($request->all());

        return $this->success($data, 200);
    }
}
