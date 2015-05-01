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

Route::get('/', 'WelcomeController@index');

Route::group(['prefix' => '/api/v1'], function () {

    // get list of conversations
    Route::get('convos', [
        'before' => 'oauth',
        'uses' => 'ConvosController@find'
    ]);

    // create a new conversation
    Route::post('convos', [
        'before' => 'oauth',
        'uses' => 'ConvosController@create'
    ]);

    // get conversation details
    Route::get('convos/{id}', [
        'before' => 'oauth',
        'uses' => 'ConvosController@get'
    ])->where('id', '[0-9]+');

    // update conversation
    Route::put('convos/{id}', [
        'before' => 'oauth',
        'uses' => 'ConvosController@update'
    ])->where('id', '[0-9]+');

    // delete conversation
    Route::put('convos/{id}', [
        'before' => 'oauth',
        'uses' => 'ConvosController@delete'
    ])->where('id', '[0-9]+');

    // create a new message

    // get conversation messages

    // delete message

});

Route::post('oauth/access_token', function () {
    return Response::json(Authorizer::issueAccessToken());
});