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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
Route::get('/sendNotification', 'NotificationController@sendToSingle')->name('sendNotification');

Route::post('/login',[
'as' =>'login.login',
'uses'=>'Api\Auth\LoginController@login'
]);

Route::post('/register',[
    'as' =>'register.register',
    'uses'=>'Api\Auth\RegisterController@register'
]);

Route::get('/profile/{id}', 'Api\UserController@profile')->name('profile');
Route::get('/profile',[
    'as' =>'user.profile',
    'uses'=>'Api\UserController@profile'
]);
Route::post('/updateFCM', 'Api\UserController@updateFCM')->name('updateFCM');
Route::post('/updateProfilePhoto', 'Api\UserController@updateProfilePhoto')->name('updatePhoto');
Route::post('/forgetPassword', 'Api\\Auth\ResetPasswordController@sendResetPasswordEmail')->name('forgetPassword');
Route::post('/resetPassword', 'Api\Auth\ResetPasswordController@resetPassword')->name('resetPassword');
Route::post('/updateProfile', 'Api\UserController@updateProfile')->name('updateProfile');
Route::post('/updatePassword', 'Api\Auth\ResetPasswordController@updatePassword')->name('updatePassword');
Route::post('/updateCover', 'Api\UserController@updateCoverPhoto')->name('updateCover');
Route::get('/search', 'Api\UserController@search')->name('search');
Route::post('/block', 'Api\UserController@block')->name('block');
Route::get('/blocked', 'Api\UserController@blocked')->name('blocked');
Route::get('/unblock/{id}', 'Api\UserController@unblock')->name('unblock');
Route::post('/report', 'Api\UserController@report')->name('report');
Route::get('/logout', 'Api\UserController@logout')->name('logout');

//FOLLOWER
Route::get('/follow/{id}', 'Api\FollowerController@follow')->name('follow');
Route::get('/followers', 'Api\FollowerController@followers')->name('followers');
Route::get('/followers/{id}', 'Api\FollowerController@followers')->name('followers');
Route::get('/unfollow/{id}', 'Api\FollowerController@unfollow')->name('unfollow');

//CATEGORIES
Route::get('/categories', 'Api\CategoryController@categories')->name('categories');

//COMMENTS
Route::get('/comments/profile/{id}', 'Api\CommentController@comments')->name('comments');
Route::get('/comments/{id}', 'Api\CommentController@singleComment')->name('singleComment');
Route::post('/comment', 'Api\CommentController@comment')->name('comment');
Route::post('/comment/edit', 'Api\CommentController@editComment')->name('editComment');
Route::post('/reply', 'Api\CommentController@reply')->name('reply');
Route::post('/reply/edit', 'Api\CommentController@editReply')->name('editReply');
Route::get('/comment/delete/{id}', 'Api\CommentController@destroy')->name('delete');
Route::get('/reply/delete/{id}', 'Api\CommentController@deleteReply')->name('delete');
Route::post('/tag', 'Api\CommentController@tag')->name('tag');
Route::get('comment/{commentID}/reaction/{reactionID}', 'Api\CommentController@reaction')->name("reaction");

//EVENTS
Route::post('/event/create', 'Api\EventController@create')->name('event');
Route::get('/events', 'Api\EventController@events')->name('events');
Route::get('/event/{id}', 'Api\EventController@event')->name('event');
Route::post('/event/checkin', 'Api\EventController@checkin')->name('checkin');
Route::post('/event/stream', 'Api\EventController@streamStart')->name('streamStart');
Route::get('/event/complete/{id}', 'Api\EventController@complete')->name('eventComplete');
Route::post('/event/buy', 'Api\EventController@buy')->name('buy');

//STREAMS
Route::get('/streams', 'Api\StreamController@streams')->name('stream');
Route::get('/streams/profile/{id}', 'Api\StreamController@streams')->name('stream');
Route::get('/streams/{id}', 'Api\StreamController@streams')->name('stream');
Route::post('/stream/create', 'Api\StreamController@store')->name('stream');
Route::get('/stream/start/{id}', 'Api\StreamController@start')->name('start');
Route::get('/stream/complete/{id}', 'Api\StreamController@complete')->name('complete');
Route::post('/stream/buy', 'Api\StreamController@buy')->name('buy');


//DONATIONS
Route::post('/stream/donate', 'Api\StreamController@donate')->name('donate');

//SETTINGS
Route::get('/setting', 'Api\SettingController@index')->name('setting');


//NOTIFICATIONS
Route::get('/notifications', 'Api\NotificationController@index')->name('notifications');
Route::post('/notification/test', 'Api\NotificationController@test')->name('test');



//WITHDRAWLS
Route::get('/withdrawls', 'Api\WithdrawlController@index')->name('withdrawls');
Route::post('/withdrawl/request', 'Api\WithdrawlController@request')->name('withdrawlRequest');
Route::post('/withdrawl/edit', 'Api\WithdrawlController@update')->name('withdrawlUpdate');


