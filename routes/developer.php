<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Developer\DeveloperController;

Route::redirect('/', 'developer/login');

Route::controller(DeveloperController::class)->group(function (): void {
    Route::get('login', 'loginPage')->name('developer.login');
    Route::post('login', 'login')->name('developer.login.attempt');

    Route::middleware('developer')->group(function (): void {
        Route::get('dashboard', 'dashboard')->name('developer.dashboard');
        Route::post('logout', 'logout')->name('developer.logout');
    });
});
