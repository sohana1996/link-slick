<?php
Route::post('app/create/domain', 'DomainController@store');
Route::post('app/edit/domain', 'DomainController@update');
Route::post('app/remove/domain', 'DomainController@remove');
Route::get('app/get/domain/all', 'DomainController@getAllDomain');
Route::get('app/get/domain/{pageNo}', 'DomainController@getDomain');
Route::get('app/get/single/domain/{id}', 'DomainController@getSingleDomain');
