<?php
Route::post('app/create/source', 'SourceController@store');
Route::post('app/edit/source', 'SourceController@update');
Route::post('app/remove/source', 'SourceController@remove');
Route::get('app/get/source/all', 'SourceController@getAllSource');
Route::get('app/get/source/{pageNo}', 'SourceController@getSource');
Route::get('app/get/single/source/{id}', 'SourceController@getSingleUrl');
