<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\Group;
use App\Http\Requests\Auth\VerifyOtp;
use App\Http\Requests\Auth\Login as LoginRequest;
use App\Http\Resources\User\Resource as UserResource;
use App\Http\Requests\Auth\Register as RegisterRequest;
use App\Http\Requests\Auth\ResetPassword as ResetPasswordRequest;
use App\Http\Requests\Auth\ForgetPassword as ForgetPasswordRequest;

/**
 * @tags Auth
 */
#[Group('Auth', weight: 10)]
class AuthController extends Controller
{
    use ApiResponser;

    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService;
    }

    /**
     * Register.
     *
     * @unauthenticated
     *
     * @response array{message: string, data: UserResource, token: string}
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->validated());

        return $this->success($data, 200);
    }

    /**
     * Verify reset OTP.
     *
     * @unauthenticated
     *
     * @response array{message: string, token: string}
     */
    public function forgotPasswordOTPVerify(VerifyOtp $request): JsonResponse
    {
        $data = $this->authService->forgotPasswordOTPVerify($request->validated());

        return $this->success($data);
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

        return $this->success($data, 200);
    }

    /**
     * Forgot password.
     *
     * @unauthenticated
     *
     * @response array{message: string}
     */
    public function forgotPassword(ForgetPasswordRequest $request): JsonResponse
    {
        $data = $this->authService->forgotPassword($request->validated());

        return $this->success($data, 200);
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

        return $this->success($data, 200);
    }

    /**
     * Logout.
     *
     * @response array{message: string}
     */
    public function logout(Request $request): JsonResponse
    {
        $data = $this->authService->logout($request->all());

        return $this->success($data, 200);
    }
}
