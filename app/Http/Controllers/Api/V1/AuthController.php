<?php

namespace App\Http\Controllers\Api\V1;

use \App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponser;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
}
