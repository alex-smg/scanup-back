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

$router->group(['prefix' => 'api'], function() use($router) {

    $router->get('/organizations', 'OrganizationController@index');
    $router->get('/organizations/{id}', 'OrganizationController@show');
    $router->post('/organizations', 'OrganizationController@store');
    $router->put('/organizations/{id}', 'OrganizationController@update');
    $router->delete('/organizations/{id}', 'OrganizationController@destroy');
    $router->get('/surveys', 'SurveyController@index');

});

