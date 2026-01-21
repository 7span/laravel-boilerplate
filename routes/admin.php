<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\SettingController;
use App\Http\Controllers\Api\Admin\UserStatusController;

Route::group(['middleware' => ['auth:sanctum', 'notification-read']], function () {
    Route::post('users/{user}/change-status', UserStatusController::class);

    Route::controller(SettingController::class)->group(function () {
        Route::get('settings', 'index');
        Route::put('settings', 'update');
    });
});
