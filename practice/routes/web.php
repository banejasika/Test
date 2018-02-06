<?php

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

Route::get('/', 'Job\\JobsController@index');
Route::get('/search', 'Job\\JobsController@index');
Route::get('/email/approve', 'Job\\JobsController@approve');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('/jobs', 'Job\\JobsController')->middleware('auth');
Route::patch('/jobs/publishOrSpam/{id}', 'Job\\JobsController@publishOrSpam')->middleware('auth');