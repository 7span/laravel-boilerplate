<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::controller(AuthController::class)->group(function () {
    Route::post('/signup', 'signUp')->name('signup');
});