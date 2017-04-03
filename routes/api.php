<?php

Route::get('status', 'BalanceController@check');
Route::post('shrink', 'ShrinkController@shrink');
Route::post('bulk', 'ShrinkController@bulk');
Route::get('download/{shrinkFileId}/{fileName}', 'DownloadController@download');