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
use App\Http\Requests\Auth\ForgetPassword;
use App\Http\Requests\Auth\ChangePassword;

class AuthController extends Controller
{
    use ApiResponser;

    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function signUp(SignUp $request)
    {
        $data = $this->authService->signup($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    public function verifyOtp(VerifyOtp $request)
    {
        $data = $this->authService->verifyOtp($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    public function login(Login $request)
    {
        $data = $this->authService->login($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    public function forgetPassword(ForgetPassword $request)
    {
        $data = $this->authService->forgetPassword($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    public function resetPassword(ResetPassword $request)
    {
        $data = $this->authService->resetPassword($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    public function sendOtp(SendOtp $request)
    {
        $data = $this->authService->generateOtp($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }

    public function changePassword(ChangePassword $request)
    {
        $data = $this->authService->changePassword($request->all());

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
