<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\AuthService;
use OpenApi\Attributes as OA;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login as LoginRequest;
use App\Http\Requests\Auth\SignUp as SignUpRequest;
use App\Http\Requests\Auth\SendOtp as SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtp as VerifyOtpRequest;
use App\Http\Requests\Auth\ResetPassword as ResetPasswordRequest;
use App\Http\Requests\Auth\ChangePassword as ChangePasswordRequest;
use App\Http\Requests\Auth\ForgetPassword as ForgetPasswordRequest;

class AuthController extends Controller
{
    use ApiResponser;

    public function __construct(private AuthService $authService)
    {
        //
    }

    #[OA\Post(
        path: '/api/v1/signup',
        operationId: 'authSignup',
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
                required: ['first_name', 'last_name', 'username', 'country_code', 'mobile_number', 'email', 'password', 'password_confirmation'],
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
                        property: 'mobile_number',
                        type: 'string',
                        format: 'mobile_number',
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
    public function signUp(SignUpRequest $request): JsonResponse
    {
        $data = $this->authService->signup($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/send-otp',
        operationId: 'sendOtp',
        tags: ['Auth'],
        summary: 'Send One-Time Password (OTP)',
        description: "Sends an OTP to a user's email address for verification purposes.",
        requestBody: new OA\RequestBody(
            required: true,
            description: 'User email and purpose for requesting OTP',
            content: new OA\JsonContent(
                required: ['email', 'otp_for'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        description: "User's email address",
                        example: 'user@gmail.com'
                    ),
                    new OA\Property(
                        property: 'otp_for',
                        type: 'string',
                        enum: ['verification', 'reset_password', 'update_profile'],
                        example: 'signup'
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
    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $data = $this->authService->sendOtp($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/verify-otp',
        operationId: 'verifyOtp',
        tags: ['Auth'],
        summary: 'Verify One-Time Password (OTP)',
        description: 'Verifies an OTP submitted by a user for authentication or other purposes.',
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
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
        security: [[
            'bearerAuth' => [],
        ]]
    )]
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $data = $this->authService->verifyOtp($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/login',
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
        $data = $this->authService->login($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/forget-password',
        operationId: 'forgetPassword',
        tags: ['Auth'],
        summary: 'Forget Password',
        description: "Initiates the process to reset the user's password by sending a reset link to the provided email address.",
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
        $data = $this->authService->forgetPassword($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    #[OA\Post(
        path: '/api/v1/reset-password',
        operationId: 'resetPassword',
        tags: ['Auth'],
        summary: 'Reset Password',
        description: "Resets the user's password using the provided email, new password, and OTP code.",
        requestBody: new OA\RequestBody(
            required: true,
            description: 'User email, new password, and OTP code',
            content: new OA\JsonContent(
                required: ['email', 'password', 'password_confirmation', 'otp'],
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
                        property: 'otp',
                        type: 'string',
                        description: "OTP code sent to the user's email",
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
            ),
            new OA\Response(response: '400', description: 'Validation errors!'),
        ],
    )]
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $this->authService->resetPassword($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
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
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $data = $this->authService->changePassword($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
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

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
