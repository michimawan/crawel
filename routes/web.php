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

Route::get('/stories/create', [
    'as' => 'stories.create',
    'uses' => 'StoriesController@create'
]);

Route::post('/stories/store', [
    'as' => 'stories.store',
    'uses' => 'StoriesController@store'
]);

Route::get('/greentag', [
    'as' => 'stories.greentag',
    'uses' => 'StoriesController@greentag'
]);
Route::get('/edit', [
    'as' => 'stories.edit',
    'uses' => 'StoriesController@edit'
]);

Route::get('/mails/create', [
    'as' => 'mails.create',
    'uses' => 'MailsController@create'
]);

Route::post('/mails/send', [
    'as' => 'mails.send',
    'uses' => 'MailsController@send'
]);


Route::get('/auth', [
    'as' => 'mails.oauth',
    'uses' => 'MailsController@auth'
]);

Route::resource('revisions', 'RevisionsController', [
    'except' => ['show']
]);