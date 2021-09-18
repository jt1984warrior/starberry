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
    return redirect('/users');
});


Route::get('/users', 'UsersController@index');
Route::get('/users_data', 'UsersController@getUserData')->name('userlist');
Route::post('/storeUser', 'UsersController@store')->name('storeUser');
Route::delete('/user/delete/{id}', 'UsersController@destroy');