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
Route::post('login', [DeveloperController::class, 'authenticate']);