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

Auth::routes();
// Route::get('/register', 'Auth\RegisterController@index');

Route::post('/change_lang','Controller@change_lang');
Route::post('/getDataTableLanguageFile', 'Controller@getDataTableLanguageFile');

//----------------------------------------------Home Page-----------------------------------------------------//
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
//------------------------------------------------------------------------------------------------------------//

//---------------------------------------------- User -----------------------------------------------------//
Route::get('/user_settings', 'UserController@index');
Route::post('', 'UserController@update');
//---------------------------------------------------------------------------------------------------------//