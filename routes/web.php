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
	'as' => 'stories.index',
	'uses' => 'StoriesController@index'
]);

Route::get('/create', [
	'as' => 'stories.create',
	'uses' => 'StoriesController@create'
]);

Route::get('/greentag', [
	'as' => 'stories.greentag',
	'uses' => 'StoriesController@greentag'
]);

Route::post('/store', [
	'as' => 'stories.store',
	'uses' => 'StoriesController@store'
]);
