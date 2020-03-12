<?php
Route::post('app/generate/url', 'UrlController@generate');
Route::post('app/generate/domain/url', 'UrlController@generateWithDomain');
Route::post('app/create/url', 'UrlController@store');
Route::post('app/edit/url', 'UrlController@update');
Route::post('app/remove/url', 'UrlController@remove');
Route::get('app/get/url/all', 'UrlController@getAllUrl');
Route::get('app/get/url/{pageNo}', 'UrlController@getUrl');
Route::get('app/get/single/url/{id}', 'UrlController@getSingleUrl');
Route::get('app/get/total/visit', 'UrlController@getTotalVisit');


Route::get('app/get/visitInfo/{id}', 'UrlController@visitFrame');
