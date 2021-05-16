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
$router->get('/units/{id}', 'UnitController@show');
$router->post('/units', 'UnitController@store');
$router->put('/units/{id}', 'UnitController@update');
$router->delete('/units/{id}', 'UnitController@delete');

// Productos
$router->get('/products', 'ProductController@index');
$router->get('/products/{id}', 'ProductController@show');
$router->post('/products', 'ProductController@store');
$router->put('/products/{id}', 'ProductController@update');
$router->delete('/products/{id}', 'ProductController@delete');

// Inventarios
$router->get('/inventories', 'InventoryController@index');
$router->get('/inventories/{productId}', 'InventoryController@show');
$router->post('/add-entry', 'InventoryController@handleInventoryEntry');
$router->post('/add-exit', 'InventoryController@handleInventoryExit');

// Depósitos
$router->get('/deposits', 'DepositController@index');
$router->get('/deposits/{id}', 'DepositController@show');
$router->post('/deposits', 'DepositController@store');
$router->put('/deposits/{id}', 'DepositController@update');
$router->delete('/deposits/{id}', 'DepositController@delete');
