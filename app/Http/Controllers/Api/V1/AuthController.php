<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Traits\ApiResponser;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Resources\User\Resource as UserResource;

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

    /**
     * Login.
     *
     * @unauthenticated
     *
     * @response array{message: string, data: UserResource, token: string}
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->login($request->validated());

        return $this->success($data);
    }

    /**
     * Forgot password.
     *
     * Sends a one time password to the given email address.
     *
     * @unauthenticated
     *
     * @response array{message: string}
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $data = $this->authService->forgotPassword($request->validated());

        return $this->success($data);
    }

    /**
     * Verify forgot password OTP.
     *
     * Returns the password reset token to be used by the reset password endpoint.
     *
     * @unauthenticated
     *
     * @response array{message: string, token: string}
     */
    public function verifyForgotPasswordOtp(VerifyOtpRequest $request): JsonResponse
    {
        $data = $this->authService->verifyForgotPasswordOtp($request->validated());

        return $this->success($data);
    }

    /**
     * Reset password.
     *
     * @unauthenticated
     *
     * @response array{message: string}
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $this->authService->resetPassword($request->validated());

        return $this->success($data);
    }

    /**
     * Logout.
     *
     * Revokes the access token used to make the request.
     *
     * @response array{message: string}
     */
    public function logout(): JsonResponse
    {
        $data = $this->authService->logout(auth()->user());

        return $this->success($data);
    }
}
