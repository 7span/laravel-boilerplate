<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\SignedUrlController;

Route::controller(AuthController::class)->group(function (): void {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');

    Route::post('forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('forgot-password/verify-otp', 'verifyForgotPasswordOtp')->name('forgot-password.verify-otp');
    Route::post('reset-password', 'resetPassword')->name('reset-password');
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::controller(UserController::class)->group(function (): void {
        Route::get('me', 'me')->name('me');
        Route::post('me', 'updateProfile')->name('me.update');
        Route::post('change-password', 'changePassword')->name('change-password');
        Route::post('locale', 'updateLocale')->name('locale');
    });

    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
});

Route::post('generate-signed-url', SignedUrlController::class)->name('generate-signed-url');
