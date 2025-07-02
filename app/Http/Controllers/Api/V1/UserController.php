<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\UserService;
use OpenApi\Attributes as OA;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePassword;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\UpdateProfile;
use App\Http\Requests\User\ChangePassword as UserChangePassword;
use App\Http\Resources\User\Resource as UserResource;

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
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success'
            ),
        ],
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
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['first_name', 'last_name', 'username', 'email'],
                properties: [
                    new OA\Property(
                        property: 'first_name',
                        type: 'string',
                        format: 'first_name',
                        example: 'Test'
                    ),
                    new OA\Property(
                        property: 'last_name',
                        type: 'string',
                        format: 'last_name',
                        example: 'User'
                    ),
                    new OA\Property(
                        property: 'username',
                        type: 'string',
                        format: 'username',
                        example: 'user12'
                    ),
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        example: 'test@gmail.com'
                    ),
                    new OA\Property(
                        property: 'mobile_no',
                        type: 'string',
                        format: 'mobile',
                        example: '9090909090'
                    ),
                ]
            ),
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
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
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Current password and new password',
            content: new OA\JsonContent(
                required: ['current_password', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(
                        property: 'current_password',
                        type: 'string',
                        description: "User's current password",
                        example: 'oldpassword123',
                        minLength: 8,
                        maxLength: 255
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        description: "User's new password",
                        example: 'newpassword123',
                        minLength: 8,
                        maxLength: 255
                    ),
                    new OA\Property(
                        property: 'password_confirmation',
                        type: 'string',
                        description: "Confirmation of the user's new password",
                        example: 'newpassword123',
                        minLength: 8,
                        maxLength: 255
                    ),
                ]
            ),
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
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
