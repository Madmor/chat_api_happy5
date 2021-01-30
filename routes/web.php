<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

/**
 * API Version 1.0
 * Bahrum Saleh Sinaga
 */

$router->group([
    'prefix' => 'api',
    'namespace' => 'API\\v1\\'
], function () use ($router) {
    // Route Tidak Harus Login
    $router->post('login',      'AuthController@login');
    $router->post('register',   'AuthController@register');

    // Route Harus Login
    $router->group([
        'middleware' => 'auth.api'
    ], function () use ($router) {
        $router->post('logout',         'AuthController@logout');
        $router->get('me', 				'AuthController@me');
        $router->get('refresh_token', 	'AuthController@refresh');

        // Kirim, Balas, Ubah dan Hapus Pesan
        $router->post('message/send',       'MessageController@send');
        $router->put('message/edit',        'MessageController@edit');
        $router->delete('message/delete',   'MessageController@delete');
        $router->post('message/reply',      'MessageController@reply');
        
        // List percakapan dan detail percakapan
        $router->get('conversation/list',           'ConversationController@list');
        $router->get('conversation/detail/{chat_room_id}', 'ConversationController@detail');
    });
});

$router->get('user',       'API\v1\UserController@list');
