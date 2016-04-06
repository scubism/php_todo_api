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

$app->group(['prefix' => 'v1'], function () use ($app) {
    $app->get('index', 'App\Http\Controllers\TodosController@index');
    $app->get('todos', 'App\Http\Controllers\TodosController@indexTodos');
    $app->get('todos/{id}', 'App\Http\Controllers\TodosController@viewTodo');
    $app->post('todos', 'App\Http\Controllers\TodosController@createTodo');
    $app->put('todos/{id}', 'App\Http\Controllers\TodosController@updateTodo');
    $app->delete('todos/{id}', 'App\Http\Controllers\TodosController@deleteTodo');
    $app->post('todos/{id}/move', 'App\Http\Controllers\TodosController@moveTodo');
});
