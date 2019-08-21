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

Route::get('/', function () {
    return view('welcome');
});
Route::get('dashboard', 'CMSController@dashboard')->name('home');

Route::group( ['middleware' => ['auth']], function() {
	Route::resource('users', 'UserController');
    Route::resource('roles', 'RolesController');
	
	Route::resource('usermgmt', 'CMS\UsermgmtController');
	Route::resource('clt', 'CMS\CLTController');

	Route::get('mandorPlantcareReport', 'CMS\TransReportController@mandorPlantcare')->name('mandorPlantcareReport.index');
	Route::get('mandorFruitcareReport', 'CMS\TransReportController@mandorFruitcare')->name('mandorFruitcareReport.index');
	Route::get('mandorPanenReport', 'CMS\TransReportController@mandorPanen')->name('mandorPanenReport.index');
	Route::post('exportMandor', 'CMS\TransReportController@exportMandor')->name('exportMandor');

	Route::get('kawilPlantcareReport', 'CMS\TransReportController@kawilPlantcare')->name('kawilPlantcareReport.index');
	Route::get('kawilFruitcareReport', 'CMS\TransReportController@kawilFruitcare')->name('kawilFruitcareReport.index');
	Route::get('kawilPanenReport', 'CMS\TransReportController@kawilPanen')->name('kawilPanenReport.index');
	Route::post('exportKawil', 'CMS\TransReportController@exportKawil')->name('exportKawil');

	Route::get('phtbReport', 'CMS\TransReportController@phtbReport')->name('phtbReport.index');
	Route::get('phbtReport', 'CMS\TransReportController@phbtReport')->name('phbtReport.index');
	Route::get('phbbReport', 'CMS\TransReportController@phbbReport')->name('phbbReport.index');
	Route::get('phhtReport', 'CMS\TransReportController@phhtReport')->name('phhtReport.index');
	Route::get('phcltReport', 'CMS\TransReportController@phcltReport')->name('phcltReport.index');
	Route::post('exportPH', 'CMS\TransReportController@exportPH')->name('exportPH');

	Route::get('spiMandorReport', 'CMS\TransReportController@spiMandorReport')->name('spiMandorReport.index');
	Route::get('spiSensusReport', 'CMS\TransReportController@spiSensusReport')->name('spiSensusReport.index');
	Route::post('exportSPI', 'CMS\TransReportController@exportSPI')->name('exportSPI');

	Route::get('rkmReport', 'CMS\RKMReportController@index')->name('rkmReport.index');
	Route::post('exportRKM', 'CMS\RKMReportController@exportRKM')->name('exportRKM');

	Route::get('customReport', 'CMS\CustomReportController@index')->name('customReport.index');
	Route::post('postDropdown', 'CMS\CustomReportController@postDropdown')->name('postDropdown');
	Route::post('postFilter', 'CMS\CustomReportController@postFilter')->name('postFilter');
	Route::post('filterByDate', 'CMS\CustomReportController@filterByDate')->name('filterByDate');
	Route::get('getDetilBlok', 'CMS\CustomReportController@getDetilBlok')->name('getDetilBlok');
	Route::get('getDetilPokok', 'CMS\CustomReportController@getDetilPokok')->name('getDetilPokok');
	Route::post('exportCustom', 'CMS\CustomReportController@exportCustom')->name('exportCustom');
	
	Route::post('chartDataSet', 'CMS\CustomReportController@chartDataSet')->name('getDataChart');

	Route::get('apk', 'CMS\FileController@index')->name('apk');
	Route::post('uploadApkRKH', 'CMS\FileController@uploadApkRKH')->name('uploadApkRKH');
	Route::post('uploadApkPH', 'CMS\FileController@uploadApkPH')->name('uploadApkPH');
});