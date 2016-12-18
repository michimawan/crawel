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
    'as' => 'revisions.index',
    'uses' => 'RevisionsController@index'
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
    'except' => ['show', 'destroy', 'index']
]);

