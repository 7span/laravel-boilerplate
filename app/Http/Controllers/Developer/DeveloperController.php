<?php

namespace App\Http\Controllers\Developer;

use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Developer\LoginRequest;

class DeveloperController extends Controller
{
    public function login(LoginRequest $request): RedirectResponse
    {
        $valid = config('developer.username') === $request->username
            && config('developer.password') === $request->password;

        if (! $valid) {
            return redirect()->back()->withErrors(['message' => __('message.invalid_credentials')]);
        }

        session()->put(config('developer.sessionKey'), true);

        return redirect()->route('developer.dashboard');
    }

    public function loginPage(): View|RedirectResponse
    {
        if (session()->has(config('developer.sessionKey'))) {
            return redirect()->route('developer.dashboard');
        }

        return view('developer.pages.login');
    }

    public function dashboard(): View
    {
        return view('developer.pages.dashboard');
    }

    public function logout(): RedirectResponse
    {
        session()->forget(config('developer.sessionKey'));

        return redirect()->route('developer.login');
    }
}
