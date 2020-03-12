<?php
Route::post('app/create/cat', 'CatController@store');
Route::post('app/edit/cat', 'CatController@update');
Route::post('app/remove/cat', 'CatController@remove');
Route::get('app/get/cat/all', 'CatController@getAllSource');
Route::get('app/get/cat/{pageNo}', 'CatController@getSource');
Route::get('app/get/single/cat/{id}', 'CatController@getSingleUrl');
