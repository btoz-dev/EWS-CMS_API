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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/dashboard', 'CMSController@dashboard');#->name('home');

Route::resource('usermgmt', 'CMS\UsermgmtController');

Route::get('/reports/{dateStart?}/{dateEnd?}', 'CMSController@reports')
	->where([
		'dateStart' => '^\d{1,2}\-\d{1,2}\-\d{4}$',
		'dateEnd' => '^\d{1,2}\-\d{1,2}\-\d{4}$'
	]);

Route::resource('article', 'CMSController');

