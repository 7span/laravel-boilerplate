<?php

namespace App\Http\Controllers\Developer;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Developer\LoginRequest;

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
