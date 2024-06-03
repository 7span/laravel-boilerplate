<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\Login;

class DeveloperController extends Controller
{
    public function login(Login $request)
    {
        $auth = resolve('littlegatekeeper');
        $loginSuccess = $auth->attempt($request->toArray());
        if (! $loginSuccess) {
            return redirect()->back()->withErrors(['message', 'Invalid credencials.']);
        }

        return redirect()->route('developer.dashboard');
    }

    public function loginPage()
    {
        return view('developer.pages.login');
    }

    public function dashboard()
    {
        return view('developer.pages.dashboard');
    }
}
