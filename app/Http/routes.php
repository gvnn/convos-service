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


$app->group(['prefix' => 'api/v1'], function ($app) {

    $app->post('users', 'App\Http\Controllers\UserController@create');
    $app->delete('users/{id:[0-9]+}', 'App\Http\Controllers\UserController@delete');

});

$app->group(['prefix' => 'api/v1', 'middleware' => 'auth'], function($app)
{
    $app->get('convos', 'App\Http\Controllers\ConvoController@get_convos');

});

$app->get('/', function () use ($app) {
    return $app->welcome();
});
