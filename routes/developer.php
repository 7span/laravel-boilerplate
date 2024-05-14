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
Route::get('login', [DeveloperController::class, 'loginPage']);
Route::post('login', [DeveloperController::class, 'login'])->name('developer.login');
Route::get('dashboard', [DeveloperController::class, 'dashboard'])->name('developer.dashboard')->middleware('developer');
