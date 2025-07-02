<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = User::first();

    return view('emails.welcome-user', [
        'user' => $user,
        'name' => 'John Doe',
    ]);
    return view('welcome');
});
