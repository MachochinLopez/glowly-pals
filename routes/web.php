<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

// Unidades.
$router->get('/units', 'UnitController@index');
$router->post('/units', 'UnitController@store');
$router->put('/units/{id}', 'UnitController@update');
$router->delete('/units/{id}', 'UnitController@delete');

// Productos
$router->get('/products', 'ProductController@index');
$router->post('/products', 'ProductController@store');
$router->put('/products/{id}', 'ProductController@update');
$router->delete('/products/{id}', 'ProductController@delete');
