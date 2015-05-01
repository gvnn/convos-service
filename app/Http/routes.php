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
        'uses' => 'ConvosController@all'
    ]);

    // create a new conversation

    // get conversation details

    // update conversation

    // create a new message

    // get conversation messages

    // delete message

    // delete conversation

});

Route::post('oauth/access_token', function () {
    return Response::json(Authorizer::issueAccessToken());
});