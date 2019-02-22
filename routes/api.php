<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::group(['middleware' => 'auth:api'], function(){
// 	Route::post('details', 'APIz\UserController@details');
// 	Route::resource('job', 'JobController');
// });
// Route::get('app', 'AppController@index');
Route::post('app/user', 'AppController@getUser');
Route::get('app/users', 'AppController@getAllUser');

Route::get('app/pokok', 'AppController@getAllPokok');
Route::get('app/rkm/{rkm}/{date}', 'AppController@getRKMMandor');
Route::get('app/rkmkawil/{rkmk}', 'AppController@getRKMKawil');

# Mandor Kawil
Route::post('app/storeMandor', 'AppController@storeMandor');
Route::post('app/storeKawil', 'AppController@storeKawil');

# Packing House
Route::post('app/storeBeratTandan', 'AppController@storeBT');
Route::post('app/storeHitungTandan', 'AppController@storePH');
Route::post('app/storeCekListTimbang', 'AppController@storeCT');

# SPI
Route::post('app/storeSensus', 'AppController@storeSENSUS');
Route::post('app/storeCorrAct', 'AppController@storeCA');

// Route::resource('plantloc', 'PlantLocController');
// Route::get('plantloc/{block}/{plot}', 'PlantLocController@show');
// Route::get('plantloc/{block}/{plot}/{baris}', 'PlantLocController@show');

// Route::get('/users', 'UserController@all');
