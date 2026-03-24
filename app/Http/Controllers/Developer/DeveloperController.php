<?php

declare(strict_types=1);

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\Login;

class DeveloperController extends Controller
{
    public function login(Login $request): \Illuminate\Http\RedirectResponse
    {
        $auth = resolve('littlegatekeeper');
        $loginSuccess = $auth->attempt($request->toArray());
        if (! $loginSuccess) {
            return redirect()->back()->withErrors(['message', 'Invalid credencials.']);
        }

        return redirect()->route('developer.dashboard');
    }

    public function loginPage(): \Illuminate\Http\RedirectResponse|\Illuminate\View\View
    {
        $auth = resolve('littlegatekeeper');
        if ($auth->isAuthenticated()) {
            return redirect()->route('developer.dashboard');
        }

        return view('developer.pages.login');
    }

    public function dashboard(): \Illuminate\View\View
    {
        return view('developer.pages.dashboard');
    }

    public function logout(): \Illuminate\Http\RedirectResponse
    {
        $auth = resolve('littlegatekeeper');
        $auth->logout();

        return redirect()->route('developer.login');
    }
}
