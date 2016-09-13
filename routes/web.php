<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', [
	'as' => 'crawler.index',
	'uses' => 'CrawlersController@index'
]);

Route::get('/create', [
	'as' => 'crawler.create',
	'uses' => 'CrawlersController@create'
]);

Route::post('/store', [
	'as' => 'crawler.store',
	'uses' => 'CrawlersController@store'
]);
