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

Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::post('register', 'Auth\RegisterController@register');

Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('auth/reset/{token}', 'HomeController@index')->name('password.reset');
Route::get('password/verify/{token}', 'Auth\ResetPasswordController@verify');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');


Route::get('ads', 'AdsController@show');
//Route::get('ad/{name}', 'AdsController@serveAdAsset');

Route::post('shrink', 'ShrinkController@create');
Route::get('shrink/{shrink}', 'ShrinkController@show');
Route::post('shrink/{shrink}/upload', 'ShrinkController@upload');

Route::get('shrink/{shrinkId}/download/{fileId?}', 'DownloadController@download');

Route::get('page/{name}', 'PageController@show');

Route::get('account/status', 'AccountController@status')->middleware(['auth']);

Route::post('subscribe', 'SubscriptionController@subscribe');

Route::get('/{subs?}', 'HomeController@index')->where(['subs' => '.*']);

