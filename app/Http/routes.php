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

$app->get('/', function () use ($app) {
    return $app->welcome();
});

$app->get('login', ['as' => 'auth.login', 'uses' => 'App\Http\Controllers\AuthController@login']);
$app->post('authenticate', ['as' => 'auth.authenticate', 'uses' => 'App\Http\Controllers\AuthController@authenticate']);

$app->get('register', ['as' => 'user.create', 'uses' => 'App\Http\Controllers\UserController@create']);
$app->get('user/{id}/edit', ['as' => 'user.edit', 'uses' => 'App\Http\Controllers\UserController@edit']);

$app->get('users', ['as' => 'user.index', 'uses' => 'App\Http\Controllers\UserController@index']);
$app->post('users', ['as' => 'user.store', 'uses' => 'App\Http\Controllers\UserController@store']);
$app->get('user/{id}', ['as' => 'user.show', 'uses' => 'App\Http\Controllers\UserController@show']);
$app->put('user/{id}', ['as' => 'user.update', 'uses' => 'App\Http\Controllers\UserController@update']);
$app->delete('user/{id}', ['as' => 'user.destroy', 'uses' => 'App\Http\Controllers\UserController@destroy']);
