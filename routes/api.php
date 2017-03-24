<?php

Route::get('status', 'BalanceController@check');
Route::post('shrink', 'ShrinkController@shrink');