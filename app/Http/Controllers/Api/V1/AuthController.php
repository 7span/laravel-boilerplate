<?php

namespace App\Http\Controllers\Api\V1;

use Auth;
use App\Traits\ApiResponser;
use App\Services\AuthService;
use App\Http\Requests\Auth\Login;
use App\Http\Requests\Auth\Signup;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\Resource as UserResource;


class AuthController extends Controller
{
    use ApiResponser;
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function signup(Signup $request)
    {
        $user = $this->authService->signup($request->all());
        $data = [
            'user' => new UserResource($user),
            'token' => $user->createToken(config('app.name'))->plainTextToken
        ];
        return $this->success($data, 200);
    }

    public function login(Login $request)
    {
        $user = $this->authService->login($request->all());
        $data = [
            'user' => new UserResource($user),
            'token' => $user->createToken(config('app.name'))->plainTextToken
        ];
        return $this->success($data, 200);
    }
}
