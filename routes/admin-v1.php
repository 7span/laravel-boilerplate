<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\UserStatusController;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('users/{user}/change-status', UserStatusController::class);
});
