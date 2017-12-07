<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'dapenti', 'as' => 'dapenti'], function () use ($router) {
	$router->get('/', ['as' => 'index', 'uses' => 'DapentiController@index']);
	$router->get('list/{type}', ['as' => 'list', 'uses' => 'DapentiController@list']);
	$router->get('image', ['as' => 'image', 'uses' => 'DapentiController@image']);
	$router->get('show/{id}', ['as' => 'show', 'uses' => 'DapentiController@show']);
});

