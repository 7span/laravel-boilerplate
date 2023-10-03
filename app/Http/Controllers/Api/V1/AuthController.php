<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\Auth\LoginData;
use App\Traits\ApiResponser;
use App\Data\Auth\SignUpData;
use App\Services\AuthService;
use App\Data\Auth\SendOtpData;
use App\Data\Auth\VerifyOtpData;
use App\Data\Auth\ResetPasswordData;
use App\Http\Controllers\Controller;
use App\Data\Auth\ForgetPasswordData;
use App\Data\Auth\ChangePasswordData;

class AuthController extends Controller
{
    use ApiResponser;

    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function signUp(SignUpData $request)
    {
        $data = $this->authService->signup($request->all());

        return $this->success($data, 200);
    }

    public function verifyOtp(VerifyOtpData $request)
    {
        $data = $this->authService->verifyOtp($request->all());

        return $this->success($data, 200);
    }

    public function login(LoginData $request)
    {
        $data = $this->authService->login($request->all());

        return $this->success($data, 200);
    }

    public function forgetPassword(ForgetPasswordData $request)
    {
        $data = $this->authService->forgetPassword($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    public function resetPassword(ResetPasswordData $request)
    {
        $data = $this->authService->resetPassword($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    public function sendOtp(SendOtpData $request)
    {
        $data = $this->authService->generateOtp($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    public function changePassword(ChangePasswordData $request)
    {
        $data = $this->authService->changePassword($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
