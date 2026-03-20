<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Middleware\MarkNotificationsAsRead;
use App\Http\Controllers\Api\SignedUrlController;
use App\Http\Controllers\Api\NotificationController;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('forgot-password-otp-verify', 'forgotPasswordOTPVerify');
    Route::post('reset-password', 'resetPassword');
});

Route::apiResource('languages', LanguageController::class)->only(['index', 'show']);

Route::group(['middleware' => ['auth:api', MarkNotificationsAsRead::class]], function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('me', 'me');
        Route::post('me', 'updateProfile');
        Route::post('change-password', 'changePassword');
        Route::post('locale', 'updateLocale');
    });

    Route::controller(NotificationController::class)->group(function () {
        Route::get('notifications', 'index');
        Route::get('notifications/unread-count', 'unreadCount');
        Route::post('notifications/read', 'readAllNotification');
        Route::post('notifications/unread', 'markAsUnread');
        Route::post('onesignal-player-id', 'setOnesignalData');
    });

    Route::delete('media/{media}', [MediaController::class, 'destroy']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::get('countries', CountryController::class);

Route::post('generate-signed-url', SignedUrlController::class);
