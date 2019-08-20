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

	Route::post('storeMandor', 'AppController@storeMandor');
	Route::post('storeKawil', 'AppController@storeKawil');
	Route::post('storePH', 'AppController@storePH');
	Route::post('storeSPI', 'AppController@storeSPI');

	Route::get('pokok', 'AppController@getAllPokok');
	Route::get('pokokCT', 'AppController@getCTPokok');
	Route::get('treePokok', 'AppController@getTreePokok');
	Route::get('getAllMandor', 'AppController@getAllMandor');
});

Route::group(['prefix' => 'staging', 'middleware' => 'changeDBStag'], function () { # Use DB GPS_APS_EWS
	Route::get('test', 'Api\Staging\StagController@test');

	Route::post('user', 'Api\Staging\StagController@getUser');

	Route::post('storeMandor', 'Api\Staging\StagController@storeMandor');
	Route::post('storeKawil', 'Api\Staging\StagController@storeKawil');
	Route::post('storePH', 'Api\Staging\StagController@storePH');
	Route::post('storeSPI', 'Api\Staging\StagController@storeSPI');

	Route::get('pokok', 'Api\Staging\StagController@getAllPokok');
	Route::get('pokokCT', 'Api\Staging\StagController@getCTPokok');
	Route::get('treePokok', 'Api\Staging\StagController@getTreePokok');
	Route::get('getAllMandor', 'Api\Staging\StagController@getAllMandor');
});

Route::group(['prefix' => 'dev', 'middleware' => 'changeDB'], function () { # Use DB GPS_APS_EWS
	Route::get('test', 'Api\Dev\DevController@test');

	Route::post('user', 'Api\Dev\DevController@getUser');

	Route::post('storeMandor', 'Api\Dev\DevController@storeMandor');
	Route::post('storeKawil', 'Api\Dev\DevController@storeKawil');
	Route::post('storePH', 'Api\Dev\DevController@storePH');
	Route::post('storeSPI', 'Api\Dev\DevController@storeSPI');

	Route::get('pokok', 'Api\Dev\DevController@getAllPokok');
	Route::get('pokokCT', 'Api\Dev\DevController@getCTPokok');
	Route::get('treePokok', 'Api\Dev\DevController@getTreePokok');
	Route::get('getAllMandor', 'Api\Dev\DevController@getAllMandor');
});

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact admin'], 404);
});
# API FIX EWS