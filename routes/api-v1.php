<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::controller(AuthController::class)->group(function (): void {
    Route::post('register', 'register')->name('register');
});
