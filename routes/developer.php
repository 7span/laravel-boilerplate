<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Developer\DeveloperController;

/*
|--------------------------------------------------------------------------
| Developer Routes
|--------------------------------------------------------------------------
|
| Here is where you can register developer routes for your application which
| routes are used for development.
|
*/

Route::redirect('/', 'login');
Route::get('login', [DeveloperController::class, 'showLoginForm']);
Route::post('authenticate', [DeveloperController::class, 'authenticate'])->name('authenticate');
Route::get('dashboard', [DeveloperController::class, 'showDashboard'])->name('dashboard')->middleware('developer');
