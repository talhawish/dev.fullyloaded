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


Route::post('/login',[
'as' =>'login.login',
'uses'=>'Api\Auth\LoginController@login'
]);

Route::post('/register',[
    'as' =>'register.register',
    'uses'=>'Api\Auth\RegisterController@register'
]);

Route::get('/profile',[
    'as' =>'user.profile',
    'uses'=>'Api\UserController@profile'
]);
Route::get('/profile/{id}', 'Api\UserController@profile')->name('profile');
Route::post('/updateFCM', 'Api\UserController@updateFCM')->name('updateFCM');
Route::post('/updateProfilePhoto', 'Api\UserController@updateProfilePhoto')->name('updatePhoto');
Route::post('/forgetPassword', 'Api\\Auth\ResetPasswordController@sendResetPasswordEmail')->name('forgetPassword');
Route::post('/resetPassword', 'Api\Auth\ResetPasswordController@resetPassword')->name('resetPassword');
Route::post('/updateProfile', 'Api\UserController@updateProfile')->name('updateProfile');
Route::get('/search', 'Api\UserController@search')->name('search');

//FOLLOWER
Route::get('/follow/{id}', 'Api\FollowerController@follow')->name('follow');
Route::get('/unfollow/{id}', 'Api\FollowerController@unfollow')->name('unfollow');

//CATEGORIES
Route::get('/categories', 'Api\CategoryController@categories')->name('categories');

//COMMENTS
Route::get('/comments/profile/{id}', 'Api\CommentController@comments')->name('comments');
Route::post('/comment', 'Api\CommentController@comment')->name('comment');
Route::post('/reply', 'Api\CommentController@reply')->name('reply');
Route::get('/comment/delete/{id}', 'Api\CommentController@destroy')->name('delete');
Route::get('comment/{commentID}/reaction/{reactionID}', 'Api\CommentController@reaction')->name("reaction");

//EVENTS
Route::post('/event/create', 'Api\EventController@create')->name('event');
Route::get('/events', 'Api\EventController@events')->name('events');

//STREAMS
Route::get('/streams', 'Api\StreamController@streams')->name('stream');
Route::get('/streams/profile/{id}', 'Api\StreamController@streams')->name('stream');
Route::get('/streams/{id}', 'Api\StreamController@streams')->name('stream');
Route::post('/stream/create', 'Api\StreamController@store')->name('stream');


