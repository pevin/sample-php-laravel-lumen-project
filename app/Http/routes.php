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

$api = app('Dingo\Api\Routing\Router');


$api->version('v1', function ($api) use ($app) {
    $api->get('/', function () use ($app) {
        return $app->version();
    });
});

$api->version('v1', function ($api) use ($app) {

    $api->group(['prefix' => 'employee'], function ($api) use ($app) {
        $api->post('/', 'App\Http\Controllers\EmployeeController@create');
        $api->patch('/{id}', 'App\Http\Controllers\EmployeeController@update');
        $api->get('/fields/', 'App\Http\Controllers\EmployeeController@fields');
        $api->get('/columns', 'App\Http\Controllers\EmployeeController@columns');
        $api->get('/{id}', 'App\Http\Controllers\EmployeeController@get');
        $api->post('/{id}/list', 'App\Http\Controllers\EmployeeController@getItems');
        $api->get('/user/{id}', 'App\Http\Controllers\EmployeeController@userViews');
    });
    $api->group(['prefix' => 'filter'], function ($api) use ($app) {
        $api->get('/types', 'App\Http\Controllers\FilterController@types');
        $api->get('/modes', 'App\Http\Controllers\FilterController@modes');
    });

});
