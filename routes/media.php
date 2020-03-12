<?php
Route::post('app/create/media', 'MediaController@store');
Route::post('app/edit/media', 'MediaController@update');
Route::post('app/remove/media', 'MediaController@remove');
Route::get('app/get/media/related/{source_id}', 'MediaController@getRelatedMedia');
Route::get('app/get/media/{pageNo}', 'MediaController@getMedia');
Route::get('app/get/single/media/{id}', 'MediaController@getMediaUrl');
