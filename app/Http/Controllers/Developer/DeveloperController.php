<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DeveloperController extends Controller
{
    public function showLoginForm(): View
    {
         return view('developer.login');
    }
}