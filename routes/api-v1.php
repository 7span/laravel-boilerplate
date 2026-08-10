<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::controller(AuthController::class)->group(function (): void {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');

    Route::post('forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('forgot-password/verify-otp', 'verifyForgotPasswordOtp')->name('forgot-password.verify-otp');
    Route::post('reset-password', 'resetPassword')->name('reset-password');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('logout', 'logout')->name('logout');
    });
});
