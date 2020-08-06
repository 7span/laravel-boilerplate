<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'developer'], function () {
    Route::get('login','Developer\DeveloperController@loginPage');
    Route::post('login','Developer\DeveloperController@login')->name('developer.login');
    Route::get('dashboard','Developer\DeveloperController@dashboard')->name('developer.dashboard')->middleware('developer');
});
