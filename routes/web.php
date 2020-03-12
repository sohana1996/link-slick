<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@show');

Route::get('/home', 'HomeController@show');
Route::get('/links', 'HomeController@renderLinks');
Route::get('/asset/sources', 'HomeController@renderSources');
Route::get('/asset/medium', 'HomeController@renderMedia');
Route::get('/asset/content', 'HomeController@renderContent');
Route::get('/asset/domains', 'HomeController@renderDomains');
Route::get('/asset/category', 'HomeController@renderCategory');

Route::get('/preview/{short}', 'UrlController@preView')->name('preview-short');
Route::get('/{short}', 'UrlController@urlTo');
Route::get('{domain}/{short}', 'UrlController@urlTo');
Route::get('/link/report/{id}', 'UrlController@singleUrlReport');

Route::group(['domain' => env('APP_URL')], function ($r) {
    Route::get('/', 'WelcomeController@show');
});
Route::group(['domain' => '{subdomain}.'.env('APP_URL').'/{short}'], function () {
    Route::get('/{short}', 'UrlController@urlTo');
});
Route::group(['domain' => '{domain}/{short}'], function () {
    Route::get('/{short}', 'UrlController@urlTo');
});
