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


//Auth::routes();
Route::get('ads', 'AdsController@show');
//Route::get('ad/{name}', 'AdsController@serveAdAsset');

Route::post('shrink', 'ShrinkController@create');
Route::get('shrink/{shrink}', 'ShrinkController@show');
Route::post('shrink/{shrink}/upload', 'ShrinkController@upload');
Route::get('shrink/{shrinkId}/download/{fileId?}', 'ShrinkController@download');

Route::get('/{subs?}', 'HomeController@index')->where(['subs' => '.*']);

