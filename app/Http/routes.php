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

$app->get('/', function () use ($app) {
    return 'Hello PHP TODO API';
});

$app->group(['prefix' => 'v2'], function () use ($app) {
    $app->get('/index', 'App\Http\Controllers\ExampleController@index');
});
