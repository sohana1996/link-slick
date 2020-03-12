<?php
Route::post('app/create/content', 'ContentController@store');
Route::post('app/edit/content', 'ContentController@update');
Route::post('app/remove/content', 'ContentController@remove');
Route::get('app/get/content/related', 'ContentController@getRelatedContent');
Route::get('app/get/content/{pageNo}', 'ContentController@getContent');
Route::get('app/get/single/content/{id}', 'ContentController@getContentUrl');
