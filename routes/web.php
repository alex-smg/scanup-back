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
    $router->post('/login', 'PersonController@login');
    $router->post('/persons', 'PersonController@store');

    $router->group(['middleware' => ['jwt.auth']], function() use ($router) {
        $router->get('/persons', 'PersonController@index');
        $router->get('/persons/{id}', 'PersonController@show');
        $router->get('/persons/search/{value}', 'PersonController@search');
        $router->get('/persons/searchOrganisation/{value}', 'PersonController@searchbyOrganisation');

        $router->get('/questions', 'QuestionController@index');
        $router->get('/questions/{id}', 'QuestionController@show');
        $router->get('/questions/search/{value}', 'QuestionController@search');

        $router->get('/organizations', 'OrganizationController@index');
        $router->get('/organizations/views', 'OrganizationController@indexAll');
        $router->get('/organizations/{id}', 'OrganizationController@show');
        $router->get('/organizations/search/{value}', 'OrganizationController@search');

        $router->get('/surveys', 'SurveyController@index');
        $router->get('/surveys/{id}', 'SurveyController@show');
        $router->get('/surveys/search/{value}', 'SurveyController@search');

        $router->get('/responses', 'ResponseController@index');
        $router->get('/responses/{id}', 'ResponseController@show');
        $router->get('/responses/search/{value}', 'ResponseController@search');
    });


    $router->group(['middleware' => ['jwt.auth', 'role:admin|moderator']], function() use ($router) {
        $router->put('/persons/{id}', 'PersonController@update');
        $router->delete('/persons/{id}', 'PersonController@destroy');

        $router->post('/questions', 'QuestionController@store');
        $router->put('/questions/{id}', 'QuestionController@update');
        $router->delete('/questions/{id}', 'QuestionController@destroy');

        $router->post('/organizations', 'OrganizationController@store');
        $router->post('/organizations/{id}', 'OrganizationController@update');
        $router->delete('/organizations/{id}', 'OrganizationController@destroy');

        $router->delete('/surveys/{id}', 'SurveyController@destroy');
        $router->post('/surveys', 'SurveyController@store');
        $router->put('/surveys/{id}', 'SurveyController@update');

        $router->post('/responses', 'ResponseController@store');
        $router->put('/responses/{id}', 'ResponseController@update');
        $router->delete('/responses/{id}', 'ResponseController@destroy');
    });
});

