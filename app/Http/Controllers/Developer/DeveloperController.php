<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\Login;

class DeveloperController extends Controller
{
    public function login(Login $request)
    {
        $valid = config('developer.username') === $request->username
            && config('developer.password') === $request->password;

        if (! $valid) {
            return redirect()->back()->withErrors(['message', 'Invalid credencials.']);
        }

        session()->put(config('developer.sessionKey'), true);

        return redirect()->route('developer.dashboard');
    }

    public function loginPage()
    {
        if (session()->has(config('developer.sessionKey'))) {
            return redirect()->route('developer.dashboard');
        }

        return view('developer.pages.login');
    }

    public function dashboard()
    {
        return view('developer.pages.dashboard');
    }

    public function logout()
    {
        session()->forget(config('developer.sessionKey'));

        return redirect()->route('developer.login');
    }
}
