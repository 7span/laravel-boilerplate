<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\CountryController;
use App\Http\Controllers\Api\V1\UserStatusController;
use App\Http\Controllers\Api\V1\VerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('signup', [AuthController::class, 'signUp']);
Route::post('login', [AuthController::class, 'login']);
Route::post('send-otp', [AuthController::class, 'sendOtp']);
Route::post('forget-password', [AuthController::class, 'forgetPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::apiResource('countries', CountryController::class)->only('index');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::get('me', [UserController::class, 'me']);
    Route::post('me', [UserController::class, 'updateProfile']);
    Route::post('change-password', [AuthController::class, 'changePassword']);

    Route::post('change-status', UserStatusController::class);
});

Route::get('/verify-email/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
