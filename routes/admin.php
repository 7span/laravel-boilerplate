<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\UserStatusController;

Route::group(['middleware' => ['auth:sanctum', 'notification-read']], function () {
    Route::post('users/{user}/change-status', UserStatusController::class);
});