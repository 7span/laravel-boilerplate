<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\LanguageController;
use App\Http\Controllers\Api\V1\SignedUrlController;
use App\Http\Controllers\Api\V1\NotificationController;

Route::controller(AuthController::class)->group(function (): void {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');

    Route::post('forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('forgot-password/verify-otp', 'verifyForgotPasswordOtp')->name('forgot-password.verify-otp');
    Route::post('reset-password', 'resetPassword')->name('reset-password');
});

Route::apiResource('languages', LanguageController::class)->only(['index', 'show']);

Route::get('countries', CountryController::class)->name('countries');

Route::middleware(['auth:api', 'notification-read'])->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::controller(UserController::class)->group(function (): void {
        Route::get('me', 'me')->name('me');
        Route::post('me', 'updateProfile')->name('me.update');
        Route::post('change-password', 'changePassword')->name('change-password');
        Route::post('locale', 'updateLocale')->name('locale');
    });

    Route::prefix('notifications')->name('notifications.')->controller(NotificationController::class)->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('unread-count', 'unreadCount')->name('unread-count');
        Route::post('read', 'markAsRead')->name('read');
        Route::post('unread', 'markAsUnread')->name('unread');
        Route::post('onesignal', 'setOnesignalData')->name('onesignal');
    });

    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
});

Route::post('generate-signed-url', SignedUrlController::class)->name('generate-signed-url');
