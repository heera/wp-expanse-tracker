<?php

/**
 * @var $app Alpha\Framework\Foundation\Application
 */

$app->rest->get('/dashboard', 'DashboardController@index');
$app->rest->get('/chart/data', 'DashboardController@getChartData');

$app->rest->group(function($app) {

    $app->rest->get('/', 'AccountController@index');
    
    $app->rest->get('/{id}', 'AccountController@find');

    $app->rest->post('/', 'AccountController@save');
    
    $app->rest->post('/{id}', 'AccountController@save');
    
    $app->rest->delete('/{id}', 'AccountController@delete');

})->prefix('accounts');

$app->rest->group(function($app) {

    $app->rest->get('/', 'LedgerController@index');
    
    $app->rest->get('/{id}', 'LedgerController@find');

    $app->rest->post('/', 'LedgerController@save');
    
    $app->rest->post('/{id}', 'LedgerController@save');
    
    $app->rest->delete('/{id}', 'LedgerController@delete');

})->prefix('ledgers');

$app->rest->group(function($app) {

    $app->rest->get('/', 'EntryController@index');
    
    $app->rest->get('/account/{id}', 'EntryController@entriesByAccount');

    $app->rest->get('/{id}', 'EntryController@find');
    
    $app->rest->post('/', 'EntryController@save');

    $app->rest->post('/{id}', 'EntryController@save');

    $app->rest->delete('/{id}', 'EntryController@delete');
    

})->prefix('entries');

$app->rest->group(function($app) {
    $app->rest->get('/', 'ReportController@index');
})->prefix('reports');
