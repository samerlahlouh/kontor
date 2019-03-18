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

use Educators\Http\Middleware\AdminAndAgent;
use Educators\Http\Middleware\IsAdmin;

Auth::routes();
// Route::get('/register', 'Auth\RegisterController@index');

Route::post('/change_lang','Controller@change_lang');
Route::post('/getDataTableLanguageFile', 'Controller@getDataTableLanguageFile');

//----------------------------------------------Home Page-----------------------------------------------------//
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');
//------------------------------------------------------------------------------------------------------------//

//---------------------------------------------- Current User -----------------------------------------------------//
Route::get('/user_settings', 'UserController@index_settings');
Route::post('', 'UserController@update_own_account');
//---------------------------------------------------------------------------------------------------------//

//---------------------------------------------- All Users -----------------------------------------------------//
Route::resource('users', 'UserController', ['only' => ['store', 'destroy'] ])->middleware(AdminAndAgent::class);
Route::get('/users', 'UserController@index_users')->middleware(AdminAndAgent::class);
Route::post('deactivate_user', 'UserController@deactivate_user');
Route::post('activate_user', 'UserController@activate_user');
//---------------------------------------------------------------------------------------------------------//

//---------------------------------------------- Packets -----------------------------------------------------//
Route::resource('packets', 'PacketController', ['only' => ['index', 'store', 'destroy'] ])->middleware(IsAdmin::class);
Route::post('/get_packet','PacketController@get_packet');
//---------------------------------------------------------------------------------------------------------//