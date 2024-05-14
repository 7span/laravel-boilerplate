<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use App\Services\AuthService;
use App\Http\Requests\Auth\Login;
use App\Http\Requests\Auth\SignUp;
use App\Http\Requests\Auth\SendOtp;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyOtp;
use App\Http\Requests\Auth\ResetPassword;
use App\Http\Requests\Auth\ChangePassword;
use App\Http\Requests\Auth\ForgetPassword;

class AuthController extends Controller
{
    use ApiResponser;

    public function __construct(private AuthService $authService)
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/v1/signup",
     *     operationId="authSignup",
     *     tags={"Auth"},
     *     summary="Register new user",
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass user credentials",
     *
     *         @OA\JsonContent(
     *             required={"first_name","last_name","username","country_code","mobile_number","email","password","password_confirmation"},
     *
     *             @OA\Property(
     *                 property="first_name",
     *                 type="string",
     *                 format="first_name",
     *                 example="Test"
     *             ),
     *             @OA\Property(
     *                 property="last_name",
     *                 type="string",
     *                 format="last_name",
     *                 example="User"
     *             ),
     *             @OA\Property(
     *                 property="username",
     *                 type="string",
     *                 format="username",
     *                 example="dhrumin12"
     *             ),
     *             @OA\Property(
     *                 property="country_code",
     *                 type="integer",
     *                 nullable=true,
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="test@gmail.com",
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 minLength=6,
     *                 writeOnly=true,
     *                 description="The user's password for login (not stored in plain text, consider using Laravel's `Hash` helper for secure storage)."
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 type="string",
     *                 minLength=6,
     *                 writeOnly=true,
     *                 description="Confirmation of the user's password."
     *             ),
     *             @OA\Property(
     *                 property="mobile_number",
     *                 type="string",
     *                 format="mobile_number",
     *                 example="9974572182"
     *             ),
     *         ),
     *     ),
     *
     *   @OA\Response(response="200", description="Register successful", @OA\JsonContent(
     *
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             example="Otp sent to your mail.Please Verify your account via mail."
     *         ),
     *         @OA\Property(
     *             property="data",
     *             type="object",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example=10
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 example="dhrumin1215@gmail.com"
     *             ),
     *             @OA\Property(
     *                 property="firstname",
     *                 type="string",
     *                 example="dhrumin"
     *             ),
     *             @OA\Property(
     *                 property="lastname",
     *                 type="string",
     *                 example="patel"
     *             ),
     *             @OA\Property(
     *                 property="username",
     *                 type="string",
     *                 example="dhrumin12"
     *             ),
     *             @OA\Property(
     *                 property="country_code",
     *                 type="string",
     *                 example="91"
     *             ),
     *             @OA\Property(
     *                 property="mobile_number",
     *                 type="string",
     *                 example="9898989898"
     *             )
     *         ),
     *         @OA\Property(
     *             property="token",
     *             type="string",
     *             example="11|o9sindzp0o4hWRhGldLWEDuFLG89GWYomNwGisOBd20d28c6"
     *         )
     *     )),
     *
     *     @OA\Response(response="401", description="Validation errors!"),
     *
     *     @OA\Parameter(
     *         name="X-Requested-With",
     *         in="header",
     *         required=true,
     *         description="Custom header for XMLHttpRequest",
     *
     *         @OA\Schema(
     *             type="string",
     *             default="XMLHttpRequest"
     *         )
     *     )
     * )
     */
    public function signUp(SignUp $request)
    {
        $data = $this->authService->signup($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/send-otp",
     *   operationId="sendOtp",
     *   tags={"Auth"},
     *   summary="Send One-Time Password (OTP)",
     *   description="Sends an OTP to a user's email address for verification purposes.",
     *
     *   @OA\RequestBody(
     *     required=true,
     *     description="User email and purpose for requesting OTP",
     *
     *     @OA\JsonContent(
     *       required={"email", "otp_for"},
     *
     *       @OA\Property(
     *         property="email",
     *         type="string",
     *         format="email",
     *         description="User's email address",
     *         example="user@gmail.com"
     *       ),
     *       @OA\Property(
     *         property="otp_for",
     *         type="string",
     *         description="Purpose for requesting OTP (e.g., 'signup', 'password_reset')",
     *         example="signup"
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(
     *     response="200",
     *     description="OTP sent successfully",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="OTP sent to your email address."
     *       ),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         description="OTP details (optional)",
     *         @OA\Property(
     *           property="otp",
     *           type="string",
     *           description="Generated OTP code"
     *         ),
     *         @OA\Property(
     *           property="expiry",
     *           type="string",
     *           format="date-time",
     *           description="OTP expiry time"
     *         )
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(response="400", description="Bad Request - Validation errors"),
     *   @OA\Response(response="422", description="Unprocessable Entity - Other errors (e.g., OTP generation failure)")
     * )
     */
    public function sendOtp(SendOtp $request)
    {
        $data = $this->authService->sendOtp($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/verify-otp",
     *   operationId="verifyOtp",
     *   tags={"Auth"},
     *   summary="Verify One-Time Password (OTP)",
     *   description="Verifies an OTP submitted by a user for authentication or other purposes.",
     *
     *   @OA\RequestBody(
     *     required=true,
     *     description="User email and OTP code",
     *
     *     @OA\JsonContent(
     *       required={"email", "otp"},
     *
     *       @OA\Property(
     *         property="email",
     *         type="string",
     *         format="email",
     *         description="User's email address",
     *         example="user@gmail.com"
     *       ),
     *       @OA\Property(
     *         property="otp",
     *         type="string",
     *         description="OTP code submitted by the user",
     *         example="123456",
     *         minLength=6,
     *         maxLength=6
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(
     *     response="200",
     *     description="OTP verification successful",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="OTP verified successfully."
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(response="400", description="Bad Request - Validation errors"),
     *   @OA\Response(response="401", description="Unauthorized - Invalid OTP or email"),
     *   security={{"bearerAuth":{}}},
     * )
     */
    public function verifyOtp(VerifyOtp $request)
    {
        $data = $this->authService->verifyOtp($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/login",
     *   operationId="loginUser",
     *   tags={"Auth"},
     *   summary="Login User",
     *   description="Logs in a user with email and password.",
     *
     *   @OA\RequestBody(
     *     required=true,
     *     description="User email and password",
     *
     *     @OA\JsonContent(
     *       required={"email", "password"},
     *
     *       @OA\Property(
     *         property="email",
     *         type="string",
     *         format="email",
     *         description="User's email address",
     *         example="user@gmail.com"
     *       ),
     *       @OA\Property(
     *         property="password",
     *         type="string",
     *         description="User's password",
     *         example="password123",
     *         minLength=8,
     *         maxLength=255
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(
     *     response="200",
     *     description="Login successful",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Login Successful!"
     *       ),
     *       @OA\Property(
     *         property="data",
     *         type="object",
     *         description="User data",
     *         @OA\Property(
     *           property="id",
     *           type="integer",
     *           example=10
     *         ),
     *         @OA\Property(
     *           property="email",
     *           type="string",
     *           example="dhrumin1215@gmail.com"
     *         ),
     *         @OA\Property(
     *           property="firstname",
     *           type="string",
     *           example="dhrumin"
     *         ),
     *         @OA\Property(
     *           property="lastname",
     *           type="string",
     *           example="patel"
     *         ),
     *         @OA\Property(
     *           property="username",
     *           type="string",
     *           example="username12"
     *         ),
     *         @OA\Property(
     *           property="country_code",
     *           type="string",
     *           example="91"
     *         ),
     *         @OA\Property(
     *           property="mobile_number",
     *           type="string",
     *           example="9898989898"
     *         )
     *       ),
     *       @OA\Property(
     *         property="token",
     *         type="string",
     *         example="11|o9sindzp0o4hWRhGldLWEDuFLG89GWYomNwGisOBd20d28c6"
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(response="400", description="Bad Request - Validation errors"),
     *   @OA\Response(response="401", description="Unauthorized - Invalid email or password")
     * )
     */
    public function login(Login $request)
    {
        $data = $this->authService->login($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/forget-password",
     *   operationId="forgetPassword",
     *   tags={"Auth"},
     *   summary="Forget Password",
     *   description="Initiates the process to reset the user's password by sending a reset link to the provided email address.",
     *
     *   @OA\RequestBody(
     *     required=true,
     *     description="User email",
     *
     *     @OA\JsonContent(
     *       required={"email"},
     *
     *       @OA\Property(
     *         property="email",
     *         type="string",
     *         format="email",
     *         description="User's email address",
     *         example="user@gmail.com"
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(
     *     response="200",
     *     description="Reset link sent successfully",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Forgot Password email has been sent successfully."
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(response="400", description="Bad Request - Validation errors"),
     *   @OA\Response(response="404", description="Not Found - Email not found")
     * )
     */
    public function forgetPassword(ForgetPassword $request)
    {
        $data = $this->authService->forgetPassword($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/reset-password",
     *   operationId="resetPassword",
     *   tags={"Auth"},
     *   summary="Reset Password",
     *   description="Resets the user's password using the provided email, new password, and OTP code.",
     *
     *   @OA\RequestBody(
     *     required=true,
     *     description="User email, new password, and OTP code",
     *
     *     @OA\JsonContent(
     *       required={"email", "password", "password_confirmation", "otp"},
     *
     *       @OA\Property(
     *         property="email",
     *         type="string",
     *         format="email",
     *         description="User's email address",
     *         example="user@gmail.com"
     *       ),
     *       @OA\Property(
     *         property="password",
     *         type="string",
     *         description="User's new password",
     *         example="newpassword123",
     *         minLength=8,
     *         maxLength=255
     *       ),
     *       @OA\Property(
     *         property="password_confirmation",
     *         type="string",
     *         description="Confirmation of the user's new password",
     *         example="newpassword123",
     *         minLength=8,
     *         maxLength=255
     *       ),
     *       @OA\Property(
     *         property="otp",
     *         type="string",
     *         description="OTP code sent to the user's email",
     *         example="123456",
     *         minLength=6,
     *         maxLength=6
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(
     *     response="200",
     *     description="Password reset successful",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Password changed successfully."
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(response="400", description="Bad Request - Validation errors"),
     *   @OA\Response(response="401", description="Unauthorized - Invalid email or OTP")
     * )
     */
    public function resetPassword(ResetPassword $request)
    {
        $data = $this->authService->resetPassword($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/change-password",
     *   operationId="changePassword",
     *   tags={"Auth"},
     *   summary="Change Password",
     *   description="Changes the user's password by verifying the current password and setting a new one.",
     *
     *   @OA\RequestBody(
     *     required=true,
     *     description="Current password and new password",
     *
     *     @OA\JsonContent(
     *       required={"current_password", "password", "password_confirmation"},
     *
     *       @OA\Property(
     *         property="current_password",
     *         type="string",
     *         description="User's current password",
     *         example="oldpassword123",
     *         minLength=8,
     *         maxLength=255
     *       ),
     *       @OA\Property(
     *         property="password",
     *         type="string",
     *         description="User's new password",
     *         example="newpassword123",
     *         minLength=8,
     *         maxLength=255
     *       ),
     *       @OA\Property(
     *         property="password_confirmation",
     *         type="string",
     *         description="Confirmation of the user's new password",
     *         example="newpassword123",
     *         minLength=8,
     *         maxLength=255
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(
     *     response="200",
     *     description="Password changed successful",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Password changed successfully."
     *       )
     *     )
     *   ),
     *
     *   @OA\Response(response="400", description="Bad Request - Validation errors"),
     *   @OA\Response(response="401", description="Unauthorized - Invalid current password"),
     *   security={{"bearerAuth":{}}}
     * )
     */
    public function changePassword(ChangePassword $request)
    {
        $data = $this->authService->changePassword($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/logout",
     *   operationId="logoutUser",
     *   tags={"Auth"},
     *   summary="Logout User",
     *   description="Logs out the currently authenticated user.",
     *
     *   @OA\Response(
     *     response="200",
     *     description="Logout successful",
     *
     *     @OA\JsonContent(
     *
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="Logout successful."
     *       )
     *     )
     *   ),
     *
     *  @OA\Response(response="401", description="Unauthorized - User not authenticated"),
     *  security={{"bearerAuth":{}}}
     * )
     */
    public function logout()
    {
        $data = $this->authService->logout();

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
