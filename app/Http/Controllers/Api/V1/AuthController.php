<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Http\Resources\User\Resource as UserResource;
use App\Http\Requests\Auth\Register as RegisterRequest;

/**
 * @tags Auth
 */
#[Group('Auth', weight: 10)]
class AuthController extends Controller
{
    use ApiResponser;

    public function __construct(private readonly AuthService $authService) {}

    /**
     * Register.
     *
     * @unauthenticated
     *
     * @response 201 array{message: string, data: UserResource, token: string}
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->validated());

        return $this->success($data, 201);
    }
}
