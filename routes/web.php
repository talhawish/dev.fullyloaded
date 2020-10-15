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

Route::get('/', 'AdminController@index')->name('home');
Route::get('/admin', 'AdminController@index')->name('home');

Route::get('/email', function () {
    return view('email.passwordreset');
});

/*Route::get('/', function () {
    return view('dashboard.v1');
});*/

Auth::routes();
Route::get('/dashboard/home', 'DashboardController@versionone')->name('home');
Route::get('/dashboard/table', 'DashboardController@table')->name('table');
Route::get('/dashboard/v2', 'DashboardController@versiontwo')->name('v2');
Route::get('/dashboard/v3', 'DashboardController@versionthree')->name('v3');

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');


Route::group(array('prefix' => 'dashboard'), function()
{
	 Route::get('/home', 'AdminController@index')->name('home');
       Route::get('/user/edit/{id}', 'AdminController@userEdit')->name('user/edit');
	   Route::post('/user/update', 'AdminController@userUpdate')->name('user/update');
	   Route::get('/user/delete/{id}', 'AdminController@userDelete')->name('user/delete');
	   Route::get('/users', 'AdminController@users')->name('users');
	   Route::get('/user/add', 'AdminController@userAddForm')->name('user/add');
	   Route::post('/user/add', 'AdminController@userAdd')->name('user/add');
	   Route::resource('admin', 'AdminController');



	   //Categories
	   Route::get('/categories', 'CategoryController@index')->name('categories');
	   Route::get('/category/add', 'CategoryController@create')->name('category/create');
	   Route::post('/category/add', 'CategoryController@store')->name('category/store');
	   Route::get('/category/edit/{id}', 'CategoryController@show')->name('category/show');
	   Route::get('/category/delete/{id}', 'CategoryController@destroy')->name('category/delete');
	   Route::post('/category/update', 'CategoryController@update')->name('category/update');

	   //Subcategories
	   Route::get('/subcategories', 'CategoryController@subcategories')->name('subcategories');
	   Route::get('/subcategory/add', 'CategoryController@subCreate')->name('subcategory/create');
	   Route::post('/subcategory/add', 'CategoryController@subStore')->name('subcategory/store');
	   Route::get('/subcategory/edit/{id}', 'CategoryController@subShow')->name('subcategory/show');
	   Route::get('/subcategory/delete/{id}', 'CategoryController@deleteSubCategory')->name('subcategory/delete');
	   Route::post('/subcategory/update', 'CategoryController@subUpdate')->name('subcategory/update');
	   
	   //SETTINGS
	    Route::get('/setting/terms_privacy', 'SettingController@terms_privacy')->name('terms_privacy');
		Route::post('/setting/terms_privacy', 'SettingController@edit_terms_privacy')->name('edit_terms_privacy');
		Route::get('/setting/contact_us', 'SettingController@contact_us')->name('contact_us');
		Route::post('/setting/contact_us', 'SettingController@edit_contact_us')->name('edit_contact_us');
		Route::get('/setting/help', 'SettingController@help')->name('help');
		Route::post('/setting/help', 'SettingController@edit_help')->name('edit_help');
		Route::get('/setting/password', 'AdminController@password')->name('password');
		Route::post('/setting/updatePassword', 'AdminController@updatePassword')->name('updatePassword');
		
		//Payments
	   Route::get('/payments', 'PaymentController@index')->name('payments');
	   Route::get('/payments/donations', 'PaymentController@donations')->name('donations');
	   Route::get('/payments/ppv', 'PaymentController@ppv')->name('ppv');
	   Route::get('/payments/event', 'PaymentController@event')->name('event');
	   
	  //Withdrawls
	   Route::get('/withdrawls', 'WithdrawlController@index')->name('withdrawls');
	   Route::get('/withdrawls/requests', 'WithdrawlController@requests')->name('withdrawlRequests');
	   Route::post('/withdrawls/paid', 'WithdrawlController@paid')->name('paid');
	   Route::post('/withdrawls/incorrect', 'WithdrawlController@incorrect')->name('incorrect');
});
