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

/* Public routes */
$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    /* Authentication */
    $app->get('register', ['as' => 'auth.createUser', 'uses' => 'AuthController@createUser']);
    $app->post('register', ['as' => 'auth.storeUser', 'uses' => 'AuthController@storeUser']);
    $app->get('register/confirmation', ['as' => 'auth.registerConfirmation', function () { return view('auth.register_confirmation'); }]);
    $app->get('email/confirmed/{token}', ['as' => 'auth.emailConfirmed', 'uses' => 'AuthController@emailConfirmed']);
    $app->get('login', ['as' => 'auth.createSession', 'uses' => 'AuthController@createSession']);
    $app->post('login', ['as' => 'auth.storeSession', 'uses' => 'AuthController@storeSession']);
    $app->get('logout', ['as' => 'auth.destroySession', 'uses' => 'AuthController@destroySession']);
    $app->get('account/recover', ['as' => 'auth.createRecoveryToken', 'uses' => 'AuthController@createRecoveryToken']);
    $app->post('account/recover', ['as' => 'auth.storeRecoveryToken', 'uses' => 'AuthController@storeRecoveryToken']);
    $app->get('account/recover/instructions', ['as' => 'auth.recoverInstructions', function () { return view('auth.recover_instructions'); }]);
    $app->get('account/reset/{token}', ['as' => 'auth.editPassword', 'uses' => 'AuthController@editPassword']);
    $app->put('account/reset/{token}', ['as' => 'auth.updatePassword', 'uses' => 'AuthController@updatePassword']);
    $app->get('account/reset', ['as' => 'auth.passwordReset', function () { return view('auth.password_reset'); }]);
});

/* Protected routes */
$app->group(['prefix' => 'auth', 'middleware' => 'auth', 'namespace' => 'App\Http\Controllers\Auth'], function ($app) {
    /* Users */
    $app->get('user/create', ['as' => 'user.create', 'uses' => 'UserController@create']);
    $app->get('user/{id}/edit', ['as' => 'user.edit', 'uses' => 'UserController@edit']);
    $app->get('users', ['as' => 'user.index', 'uses' => 'UserController@index']);
    $app->post('users', ['as' => 'user.store', 'uses' => 'UserController@store']);
    $app->get('user/{id}', ['as' => 'user.show', 'uses' => 'UserController@show']);
    $app->put('user/{id}', ['as' => 'user.update', 'uses' => 'UserController@update']);
    $app->delete('user/{id}', ['as' => 'user.destroy', 'uses' => 'UserController@destroy']);

    /* Posts */
    $app->get('post/create', ['as' => 'post.create', 'uses' => 'PostController@create']);
    $app->get('post/{slug}/edit', ['as' => 'post.edit', 'uses' => 'PostController@edit']);
    $app->get('posts', ['as' => 'post.index', 'uses' => 'PostController@index']);
    $app->post('posts', ['as' => 'post.store', 'uses' => 'PostController@store']);
    $app->get('post/{slug}', ['as' => 'post.show', 'uses' => 'PostController@show']);
    $app->put('post/{slug}', ['as' => 'post.update', 'uses' => 'PostController@update']);
    $app->delete('post/{slug}', ['as' => 'post.destroy', 'uses' => 'PostController@destroy']);

    /* Tags */
    $app->get('tag/create', ['as' => 'tag.create', 'uses' => 'TagController@create']);
    $app->get('tag/{id}/edit', ['as' => 'tag.edit', 'uses' => 'TagController@edit']);
    $app->get('tags', ['as' => 'tag.index', 'uses' => 'TagController@index']);
    $app->post('tags', ['as' => 'tag.store', 'uses' => 'TagController@store']);
    $app->put('tag/{id}', ['as' => 'tag.update', 'uses' => 'TagController@update']);
    $app->delete('tag/{id}', ['as' => 'tag.destroy', 'uses' => 'TagController@destroy']);
});
