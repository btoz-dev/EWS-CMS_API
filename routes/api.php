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
# API FIX EWS


#Route::post('app/storeKawil', 'AppController@storeKawil');

Route::group(['prefix' => 'app'], function () { # Use DB GPS_APS
	Route::get('test', 'AppController@test');

	Route::post('user', 'AppController@getUser');

	Route::post('user2', 'AppController@getUser2');

	Route::post('storeMandor', 'AppController@storeMandor');
	Route::post('storeKawil', 'AppController@storeKawil');

	Route::get('pokok', 'AppController@getAllPokok');
	Route::get('pokokCT', 'AppController@getCTPokok');
	Route::get('treePokok', 'AppController@getTreePokok');
});

Route::group(['prefix' => 'dev', 'middleware' => 'changeDB'], function () { # Use DB GPS_APS_EWS
	Route::get('test', 'Api\Dev\DevController@test');

	Route::post('user', 'Api\Dev\DevController@getUser');

	Route::post('user2', 'Api\Dev\DevController@getUser2');

	Route::post('storeMandor', 'Api\Dev\DevController@storeMandor');
	Route::post('storeKawil', 'Api\Dev\DevController@storeKawil');

	Route::get('pokok', 'Api\Dev\DevController@getAllPokok');
	Route::get('pokokCT', 'Api\Dev\DevController@getCTPokok');
	Route::get('treePokok', 'Api\Dev\DevController@getTreePokok');
});
# API FIX EWS

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::group(['middleware' => 'auth:api'], function(){
// 	Route::post('details', 'APIz\UserController@details');
// 	Route::resource('job', 'JobController');
// });
// Route::get('app', 'AppController@index');
// Route::post('testLogin', 'AppController@testLogin');
// Route::get('update', 'AppController@update');
/*



// Route::post('app/user2', 'AppController@getUser2');
Route::get('app/users', 'AppController@getAllUser');

Route::get('app/rkm/{rkm}/{date}', 'AppController@getRKMMandor');
Route::get('app/rkmkawil/{rkmk}', 'AppController@getRKMKawil');

# Mandor Kawil
// Route::post('storeMandor', 'AppController@storeMandor2');


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
*/