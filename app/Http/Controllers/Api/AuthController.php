<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\AuthService;
use OpenApi\Attributes as OA;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyOtp;
use App\Http\Requests\Auth\Login as LoginRequest;
use App\Http\Requests\Auth\Register as RegisterRequest;
use App\Http\Requests\Auth\ResetPassword as ResetPasswordRequest;
use App\Http\Requests\Auth\ForgetPassword as ForgetPasswordRequest;

class AuthController extends Controller
{
    use ApiResponser;

    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService;
    }

    #[OA\Post(
        path: '/api/register',
        operationId: 'authRegister',
        tags: ['Auth'],
        summary: 'Register new user',
    )]
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/forgot-password-otp-verify',
        operationId: 'forgotPasswordOTPVerify',
        tags: ['Auth'],
        summary: 'Verify OTP for password reset',
        description: 'Verifies the OTP sent to user email for password reset and returns a reset token',
    )]
    public function forgotPasswordOTPVerify(VerifyOtp $request): JsonResponse
    {
        $data = $this->authService->forgotPasswordOTPVerify($request->validated());

        return $this->success($data);
    }

    #[OA\Post(
        path: '/api/login',
        operationId: 'loginUser',
        tags: ['Auth'],
        summary: 'Login User',
        description: 'Logs in a user with email and password.',
    )]
    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/forgot-password',
        operationId: 'forgotPassword',
        tags: ['Auth'],
        summary: 'Forgot Password with otp',
        description: "Initiates the process to reset the user's password by otp.",
    )]
    public function forgotPassword(ForgetPasswordRequest $request): JsonResponse
    {
        $data = $this->authService->forgotPassword($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/reset-password',
        operationId: 'resetPassword',
        tags: ['Auth'],
        summary: 'Reset Password',
        description: "Resets the user's password using the provided email, new password via link",
    )]
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $this->authService->resetPassword($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/logout',
        operationId: 'logoutUser',
        tags: ['Auth'],
        summary: 'Logout User',
        description: 'Logs out the currently authenticated user.',
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
    public function logout(Request $request): JsonResponse
    {
        $data = $this->authService->logout($request->all());

        return $this->success($data, 200);
    }
}
