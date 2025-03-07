<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DeveloperController extends Controller
{
    public function showLoginForm(): View
    {
        return view('developer.login');
    }

    public function authenticate(LoginRequest $request): RedirectResponse
    {
        $auth = resolve('littlegatekeeper');

        if (! $auth->attempt($request->validated())) {
            return redirect()->back()
                ->withErrors(['message' => 'Invalid Credentials. Please try again.'])
                ->withInput();
        }

        return redirect()->route('developer.dashboard');
    }

    public function showDashboard(): View
    {
        return view('developer.dashboard');
    }
}
