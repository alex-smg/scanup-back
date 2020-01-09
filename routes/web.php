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

    $router->get('/surveys', 'SurveyController@index');
    $router->get('/surveys/{id}', 'SurveyController@show');
    $router->delete('/surveys/{id}', 'SurveyController@destroy');
    $router->post('/surveys', 'SurveyController@store');
    $router->put('/surveys/{id}', 'SurveyController@update');


    $router->post('/login', 'PersonController@login');

    $router->group(['middleware' => ['jwt.auth', 'role:super-admin|admin']], function() use ($router) {
        $router->get('/organizations', 'OrganizationController@index');
    });

    $router->group(['middleware' => 'jwt.auth'], function() use ($router) {
        $router->get('/organizations/{id}', 'OrganizationController@show');
        $router->post('/organizations', 'OrganizationController@store');
        $router->put('/organizations/{id}', 'OrganizationController@update');
        $router->delete('/organizations/{id}', 'OrganizationController@destroy');

        $router->get('/persons', 'PersonController@index');
        $router->get('/persons/{id}', 'PersonController@show');
        $router->post('/persons', 'PersonController@store');
        $router->put('/persons/{id}', 'PersonController@update');
        $router->delete('/persons/{id}', 'PersonController@destroy');
    });
});

