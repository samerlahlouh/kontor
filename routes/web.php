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
use Educators\Http\Middleware\Regular;

Auth::routes();

Route::post('/change_lang','Controller@change_lang');
Route::post('/getDataTableLanguageFile', 'Controller@getDataTableLanguageFile');

//----------------------------------------------Home Page-----------------------------------------------------//
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');

// Regular
Route::post('/check_number','HomeController@check_number')->middleware(Regular::class);
Route::post('/transfer_packet','HomeController@transfer_packet')->middleware(Regular::class);
Route::post('/get_packets_by_operator_and_type','HomeController@get_packets_by_operator_and_type')->middleware(Regular::class);
Route::post('/cancel_order_by_id','HomeController@cancel_order_by_id')->middleware(Regular::class);
Route::post('/make_packet_in_transfer_status','HomeController@make_packet_in_transfer_status')->middleware(Regular::class);

// Admin
Route::post('/change_order_status_by_id','HomeController@change_order_status_by_id')->middleware(IsAdmin::class);
Route::post('/change_charging_status_by_id','HomeController@change_charging_status_by_id')->middleware(IsAdmin::class);
Route::post('/send_result_to_user','HomeController@send_result_to_user')->middleware(IsAdmin::class);
Route::post('/get_unavailable_packets_by_user','HomeController@get_unavailable_packets_by_user')->middleware(IsAdmin::class);
//------------------------------------------------------------------------------------------------------------//

//---------------------------------------------- Current User -----------------------------------------------------//
Route::get('/user_settings', 'UserController@index_settings')->middleware('auth');
Route::post('', 'UserController@update_own_account')->middleware('auth');
//---------------------------------------------------------------------------------------------------------//

//---------------------------------------------- All Users -----------------------------------------------------//
Route::resource('users', 'UserController', ['only' => ['store', 'destroy'] ])->middleware(AdminAndAgent::class);
Route::get('/users', 'UserController@index_users')->middleware(AdminAndAgent::class);
Route::post('deactivate_user', 'UserController@deactivate_user')->middleware(AdminAndAgent::class);
Route::post('activate_user', 'UserController@activate_user')->middleware(AdminAndAgent::class);

// User packets
Route::get('/user_packets/{user_id}', 'UserController@index_user_packets')->middleware(AdminAndAgent::class);
Route::post('/store_user_packets','UserController@store_user_packets')->middleware(AdminAndAgent::class);
//---------------------------------------------------------------------------------------------------------//

//---------------------------------------------- Packets -----------------------------------------------------//
Route::resource('packets', 'PacketController', ['only' => ['index', 'store', 'destroy'] ])->middleware(AdminAndAgent::class);
Route::post('/get_packet','PacketController@get_packet')->middleware(AdminAndAgent::class);
Route::post('/get_notes_of_packet','PacketController@get_notes_of_packet')->middleware(AdminAndAgent::class);

// Packet users
Route::get('/packet_users/{packet_id}', 'PacketController@index_packet_users')->middleware(AdminAndAgent::class);
Route::post('/store_packet_users','PacketController@store_packet_users')->middleware(AdminAndAgent::class);

// Regular user
Route::get('/regular_packets', 'PacketController@index_regular_packets')->middleware(Regular::class);
Route::post('/store_regular_packets','PacketController@store_regular_packets')->middleware(Regular::class);

//---------------------------------------------------------------------------------------------------------//

//---------------------------------------------- Chargings -----------------------------------------------------//
Route::resource('chargings', 'ChargingController', ['only' => ['index', 'store', 'destroy'] ])->middleware(IsAdmin::class);
Route::post('/get_charging','ChargingController@get_charging')->middleware(IsAdmin::class);
Route::post('/update','ChargingController@update')->middleware(IsAdmin::class);

// Regular user
Route::get('/regular_chargings', 'ChargingController@index_regular_chargings')->middleware(Regular::class);
Route::post('/delete_charging','ChargingController@delete_charging')->middleware(Regular::class);
Route::post('/store_regular_charing','ChargingController@store_regular_charing')->middleware(Regular::class);
//---------------------------------------------------------------------------------------------------------//

//----------------------------------------------- Orders --------------------------------------------------//
Route::get('/regular_orders', 'OrderController@index')->middleware(Regular::class);
//---------------------------------------------------------------------------------------------------------//

//----------------------------------------------- Operators --------------------------------------------------//
Route::get('/operators', 'OperatorController@index')->middleware(IsAdmin::class);
Route::post('/store','OperatorController@store')->middleware(IsAdmin::class);
Route::delete('/operators/{operator}', 'OperatorController@destroy')->middleware(IsAdmin::class);
//---------------------------------------------------------------------------------------------------------//

//----------------------------------------------- Packets Types --------------------------------------------------//
Route::get('/packets_types', 'PacketTypeController@index')->middleware(IsAdmin::class);
Route::post('/packet_type_store','PacketTypeController@store')->middleware(IsAdmin::class);
Route::delete('/packets_types/{type}', 'PacketTypeController@destroy')->middleware(IsAdmin::class);
//---------------------------------------------------------------------------------------------------------//