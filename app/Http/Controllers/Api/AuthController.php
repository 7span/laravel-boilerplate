<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponser;
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
            description: 'Pass user credentials',
            content: new OA\JsonContent(
                required: ['first_name', 'last_name', 'username', 'country_code', 'mobile_no', 'email', 'password', 'password_confirmation'],
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
                        example: 'test'
                    ),
                    new OA\Property(
                        property: 'country_code',
                        type: 'integer',
                        nullable: true,
                    ),
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        example: 'test@gmail.com',
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        minLength: 6,
                        writeOnly: true,
                        description: "The user's password for login (not stored in plain text, consider using Laravel's `Hash` helper for secure storage)."
                    ),
                    new OA\Property(
                        property: 'password_confirmation',
                        type: 'string',
                        minLength: 6,
                        writeOnly: true,
                        description: "Confirmation of the user's password."
                    ),
                    new OA\Property(
                        property: 'mobile_no',
                        type: 'string',
                        format: 'mobile_no',
                        example: '9974572182'
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
        requestBody: new OA\RequestBody(
            required: true,
            description: 'User email and OTP code',
            content: new OA\JsonContent(
                required: ['email', 'otp'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        description: "User's email address",
                        example: 'user@gmail.com'
                    ),
                    new OA\Property(
                        property: 'otp',
                        type: 'string',
                        description: 'OTP code submitted by the user',
                        example: '123456',
                        minLength: 6,
                        maxLength: 6
                    ),
                ]
            ),
        ),
        responses: [
            new OA\Response(
                response: '200',
                description: 'Success.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'token', type: 'string'),
                    ]
                )
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
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
        requestBody: new OA\RequestBody(
            required: true,
            description: 'User email and password',
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        description: "User's email address",
                        example: 'user@gmail.com'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        description: "User's password",
                        example: 'password123',
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
    )]
    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/forget-password',
        operationId: 'forgetPassword',
        tags: ['Auth'],
        summary: 'Forget Password with otp',
        description: "Initiates the process to reset the user's password by otp.",
        requestBody: new OA\RequestBody(
            required: true,
            description: 'User email',
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        description: "User's email address",
                        example: 'user@gmail.com'
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
    )]
    public function forgetPassword(ForgetPasswordRequest $request): JsonResponse
    {
        $data = $this->authService->forgetPassword($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/reset-password',
        operationId: 'resetPassword',
        tags: ['Auth'],
        summary: 'Reset Password',
        description: "Resets the user's password using the provided email, new password via link",
        requestBody: new OA\RequestBody(
            required: true,
            description: 'User email, new password, and token',
            content: new OA\JsonContent(
                required: ['email', 'password', 'password_confirmation', 'token'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        description: "User's email address",
                        example: 'user@gmail.com'
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
                    new OA\Property(
                        property: 'token',
                        type: 'string',
                        description: "token sent to the user's email link",
                        example: '352a6ef197dd90f51b45e3db5bc6de',
                        minLength: 6,
                        maxLength: 6
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
    )]
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $this->authService->resetPassword($request->validated());

        return $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/logout',
        operationId: 'logoutUser',
        tags: ['Auth'],
        summary: 'Logout User',
        description: 'Logs out the currently authenticated user.',
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
    public function logout(): JsonResponse
    {
        $data = $this->authService->logout();

        return $this->success($data, 200);
    }
}
