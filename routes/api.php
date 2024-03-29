<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

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


$router->get('/', fn () => 'Api is up!');

$router->get('/balance', 'AccountController@getBalance');
$router->post('/event', 'EventController@dispatchEvent');

$router->post('/reset', 'MaintenanceController@resetCache');
