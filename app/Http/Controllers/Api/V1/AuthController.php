<?php

namespace App\Http\Controllers\Api\V1;

use App\Data\UserData;
use App\Jobs\WelcomeEmail;
use App\Jobs\ResetPassword;
use App\Data\Auth\LoginData;
use App\Traits\ApiResponser;
use App\Data\Auth\SignUpData;
use App\Services\AuthService;
use App\Data\Auth\ResetPasswordData;
use App\Http\Controllers\Controller;
use App\Data\Auth\ForgetPasswordData;

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
        $user = $this->authService->signup($request->all());
        $data = [
            'user' => UserData::from($user),
            'token' => $user->createToken(config('app.name'))->accessToken,
        ];

        WelcomeEmail::dispatch($user)->onQueue('email');

        return $this->success($data, 200);
    }

    public function login(LoginData $request)
    {
        $user = $this->authService->login($request->all());
        $data = [
            'user' => UserData::from($user),
            'token' => $user->createToken(config('app.name'))->accessToken,
        ];

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
        if (! isset($data['errors'])) {
            ResetPassword::dispatch($data['user'], $request->password)->onQueue('email');
        }

        return isset($data['errors']) ? $this->error($data) : $this->success($data, 200);
    }
}
