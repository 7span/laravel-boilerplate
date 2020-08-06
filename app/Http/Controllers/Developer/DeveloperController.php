<?php

namespace App\Http\Controllers\Developer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeveloperController extends Controller
{
    public function login(Request $request)
    {
        $auth = resolve('littlegatekeeper');
        $loginSuccess = $auth->attempt($request->only([
            'username',
            'password'
        ]));
        if (!$loginSuccess) {
            return redirect()->back()->withErrors(['msg', 'Invalid credencials.']);
        }
        return redirect()->route('developer.dashboard');
    }

    public function loginPage(Request $request)
    {
        return view('developer.pages.login');
    }

    public function dashboard(Request $request)
    {
        return view('developer.pages.dashboard');
    }
}