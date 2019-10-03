<?php


$router->group(['prefix'=>'api/v1'], function () use ($router) {
    $router->get('/categories', 'CategoryController@index');
    $router->get('/categories/{id}', 'CategoryController@show');
    $router->post('/categories', 'CategoryController@store');
    $router->put('/categories/{id}', 'CategoryController@update');
    $router->delete('/categories/{id}', 'CategoryController@delete');
});
