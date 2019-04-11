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



// Route::get('customReport', [
// 	'middleware' => ['roles'], // A 'roles' middleware must be specified
// 	'uses' => 'CMS\CustomReportController@index',
// 	'roles' => ['super admin', 'management'] // Only an administrator, or a manager can access this route
// ])->name('customReport.index');

Route::group([
	'middleware' => ['roles'], // A 'roles' middleware must be specified
	'roles' => ['super admin'] // Only an administrator, or a manager can access this route
], function() {
	Route::get('dashboard', 'CMSController@dashboard');#->name('home');
	Route::post('dashboardChartDataSet', 'CMSController@chartDataSet')->name('dashboard.getDataChart');

	Route::resource('usermgmt', 'CMS\UsermgmtController');
	Route::post('usermgmtPostRoleDropdown', 'CMS\UsermgmtController@postRoleDropdown')->name('usermgmt.postRoleDropdown');

	// Route::resource('transReport', 'CMS\TransReportController');
	Route::get('mandorPlantcareReport', 'CMS\TransReportController@mandorPlantcare')->name('mandorPlantcareReport.index');
	Route::get('kawilPlantcareReport', 'CMS\TransReportController@kawilPlantcare')->name('kawilPlantcareReport.index');

	// Route::resource('rkmReport', 'CMS\RKMReportController');
	Route::get('rkmReport', 'CMS\RKMReportController@index')->name('rkmReport.index');

	Route::get('customReport', 'CMS\CustomReportController@index')->name('customReport.index');
	Route::post('postDropdown', 'CMS\CustomReportController@postDropdown')->name('postDropdown');
	Route::post('postFilter', 'CMS\CustomReportController@postFilter')->name('postFilter');
	Route::post('filterByDate', 'CMS\CustomReportController@filterByDate')->name('filterByDate');
	
	Route::post('chartDataSet', 'CMS\CustomReportController@chartDataSet')->name('getDataChart');
});

Route::group([
	'middleware' => ['roles'], // A 'roles' middleware must be specified
	'roles' => ['management', 'kawil', 'spi'] // Only an administrator, or a manager can access this route
], function() {
	Route::get('dashboard', 'CMSController@dashboard');#->name('home');
	Route::post('dashboardChartDataSet', 'CMSController@chartDataSet')->name('dashboard.getDataChart');

	// Route::resource('transReport', 'CMS\TransReportController');
	Route::get('mandorPlantcareReport', 'CMS\TransReportController@mandorPlantcare')->name('mandorPlantcareReport.index');
	Route::get('kawilPlantcareReport', 'CMS\TransReportController@kawilPlantcare')->name('kawilPlantcareReport.index');

	// Route::resource('rkmReport', 'CMS\RKMReportController');
	Route::get('rkmReport', 'CMS\RKMReportController@index')->name('rkmReport.index');

	Route::get('customReport', 'CMS\CustomReportController@index')->name('customReport.index');
	Route::post('postDropdown', 'CMS\CustomReportController@postDropdown')->name('postDropdown');
	Route::post('postFilter', 'CMS\CustomReportController@postFilter')->name('postFilter');
	Route::post('filterByDate', 'CMS\CustomReportController@filterByDate')->name('filterByDate');
	
	Route::post('chartDataSet', 'CMS\CustomReportController@chartDataSet')->name('getDataChart');
});

// Route::get('/reports/{dateStart?}/{dateEnd?}', 'CMSController@reports')
// 	->where([
// 		'dateStart' => '^\d{1,2}\-\d{1,2}\-\d{4}$',
// 		'dateEnd' => '^\d{1,2}\-\d{1,2}\-\d{4}$'
// 	]);

// Route::resource('article', 'CMSController');

