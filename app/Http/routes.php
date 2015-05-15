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

/* Authentication */
$app->get('register', ['as' => 'auth.createUser', 'uses' => 'App\Http\Controllers\AuthController@createUser']);
$app->post('register', ['as' => 'auth.storeUser', 'uses' => 'App\Http\Controllers\AuthController@storeUser']);
$app->get('register/confirmation', ['as' => 'auth.registerConfirmation', function () { return view('auth.register_confirmation'); }]);
$app->get('email/confirmed/{token}', ['as' => 'auth.emailConfirmed', 'uses' => 'App\Http\Controllers\AuthController@emailConfirmed']);
$app->get('login', ['as' => 'auth.createSession', 'uses' => 'App\Http\Controllers\AuthController@createSession']);
$app->post('login', ['as' => 'auth.storeSession', 'uses' => 'App\Http\Controllers\AuthController@storeSession']);
$app->get('account/recover', ['as' => 'auth.createRecoveryToken', 'uses' => 'App\Http\Controllers\AuthController@createRecoveryToken']);
$app->post('account/recover', ['as' => 'auth.storeRecoveryToken', 'uses' => 'App\Http\Controllers\AuthController@storeRecoveryToken']);
$app->get('account/recover/instructions', ['as' => 'auth.recoverInstructions', function () { return view('auth.recover_instructions'); }]);
$app->get('account/reset/{token}', ['as' => 'auth.editPassword', 'uses' => 'App\Http\Controllers\AuthController@editPassword']);
$app->put('account/reset/{token}', ['as' => 'auth.updatePassword', 'uses' => 'App\Http\Controllers\AuthController@updatePassword']);
$app->get('account/reset', ['as' => 'auth.passwordReset', function () { return view('auth.password_reset'); }]);

/* Users */
$app->get('user/create', ['as' => 'user.create', 'uses' => 'App\Http\Controllers\UserController@create']);
$app->get('user/{id}/edit', ['as' => 'user.edit', 'uses' => 'App\Http\Controllers\UserController@edit']);
$app->get('users', ['as' => 'user.index', 'uses' => 'App\Http\Controllers\UserController@index']);
$app->post('users', ['as' => 'user.store', 'uses' => 'App\Http\Controllers\UserController@store']);
$app->get('user/{id}', ['as' => 'user.show', 'uses' => 'App\Http\Controllers\UserController@show']);
$app->put('user/{id}', ['as' => 'user.update', 'uses' => 'App\Http\Controllers\UserController@update']);
$app->delete('user/{id}', ['as' => 'user.destroy', 'uses' => 'App\Http\Controllers\UserController@destroy']);
