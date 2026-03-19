<?php

namespace App\Http\Controllers\Api;

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
#[Group('Auth', weight: 0)]
class AuthController extends Controller
{
    use ApiResponser;

    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService;
    }

    /**
     * Register a new user.
     *
     * Creates a new user account and returns an access token.
     *
     * @response array{message: string, data: UserResource, token: string}
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->validated());

        return $this->success($data, 200);
    }

    /**
     * Verify OTP for password reset.
     *
     * Validates the OTP sent to the user's email and returns a password-reset token.
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
     * Authenticates a user with email and password and returns an access token.
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
     * Sends a one-time passcode to the user's email to initiate the password reset flow.
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
     * Sets a new password using the reset token obtained after OTP verification.
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
     * Revokes the current access token and ends the authenticated session.
     *
     * @response array{message: string}
     */
    public function logout(Request $request): JsonResponse
    {
        $data = $this->authService->logout($request->all());

        return $this->success($data, 200);
    }
}
