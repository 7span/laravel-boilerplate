<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\LanguageController;
use App\Http\Controllers\Api\V1\SignedUrlController;
use App\Http\Controllers\Api\V1\MasterSettingController;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('send-otp', 'sendOtp');
    Route::post('forget-password', 'forgetPassword');
    Route::post('forget-password-otp-verify', 'forgotPasswordOTPVerify');
    Route::post('reset-password', 'resetPassword');
    // Route::post('reset-password-otp', 'resetPasswordOtp');
    // Route::post('forget-password', 'forgetPassword');
});

Route::apiResource('languages', LanguageController::class)->only(['index', 'show']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('me', 'me');
        Route::post('me', 'updateProfile');
        Route::post('change-password', 'changePassword');
        Route::post('logout', 'logout');
    });
});


Route::get('countries', CountryController::class);

Route::apiResource('settings', MasterSettingController::class)->only(['index', 'show']);

Route::post('generate-signed-url', SignedUrlController::class);
