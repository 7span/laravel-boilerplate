<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\SettingController;
use App\Http\Controllers\Api\V1\Admin\UserStatusController;

Route::group(['middleware' => ['auth:api', 'role:' . config('site.roles.admin'), 'notification-read']], function () {
    Route::post('users/{user}/change-status', UserStatusController::class);

    Route::controller(SettingController::class)->group(function () {
        Route::get('settings', 'index');
        Route::put('settings', 'update');
    });
});
