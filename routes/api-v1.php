<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('signup', 'AuthController@signup');
Route::post('login', 'AuthController@login');

Route::post('forget-password', 'AuthController@forgetPassword');
Route::post('reset-password', 'AuthController@resetPassword');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('me', 'UserController@me');
});