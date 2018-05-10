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

use Illuminate\Support\Facades\Artisan;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('deploy', function () {
    echo '认证成功，开始更新'."\n\r";
    echo exec("./github_pull.sh");
    echo "\n\r";
    echo date("Y-m-d H:i:s");
});

$router->post('deploy', function () {
    if (!isset($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
        die('来源非法');
    }
    $github_signa = $_SERVER['HTTP_X_HUB_SIGNATURE'];
    list($hash_type, $hash_value) = explode('=', $github_signa, 2);
    $payload = file_get_contents("php://input");
    $secret = env('APP_KEY');
    $hash = hash_hmac($hash_type, $payload, $secret);
    if ($hash && $hash === $hash_value) {
        echo '认证成功，开始更新'."\n\r";
        echo exec("./github_pull.sh");
        echo "\n\r";
        echo date("Y-m-d H:i:s");
    } else {
        echo '认证失败';
    }
});

$router->group(['prefix' => 'dapenti', 'as' => 'dapenti'], function () use ($router) {
    $router->get('/', ['as' => 'index', 'uses' => 'DapentiController@index']);
    $router->get('list/{type}', ['as' => 'list', 'uses' => 'DapentiController@list']);
    $router->get('image', ['as' => 'image', 'uses' => 'DapentiController@image']);
    $router->get('show/{id}', ['as' => 'show', 'uses' => 'DapentiController@show']);
});

$router->get('/t/{site}/{page}', function ($site, $page) {
    Artisan::call('crawler:torrents', [
            '--site' => $site,
            '--page' => $page
        ]);
});
