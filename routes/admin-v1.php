<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\SettingController;
use App\Http\Controllers\Api\V1\Admin\UserStatusController;

Route::middleware(['auth:api', 'notification-read'])->group(function (): void {
    Route::post('users/{user}/change-status', UserStatusController::class)->name('users.change-status');

    Route::controller(SettingController::class)->group(function (): void {
        Route::get('settings', 'index')->name('settings.index');
        Route::put('settings', 'update')->name('settings.update');
    });
});
