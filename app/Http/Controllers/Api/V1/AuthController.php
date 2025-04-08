<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignUpRequest;

class AuthController extends Controller
{
    use ApiResponser;
    
    public function __construct(
        private AuthService $authService,
    )
    {}

    public function signUp(SignUpRequest $request): JsonResponse
    {
        $data = $this->authService->signup($request->validated());
        return $this->success($data, 200);
    }
}
