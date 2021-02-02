<?php

/**
 * @var $router Alpha\Framework\Rest\Rest
 */

$router->get('/reports', 'ReportController@index');
$router->get('/dashboard', 'DashboardController@index');
$router->get('/chart/data', 'DashboardController@getChartData');

$router->prefix('accounts')->group(function($router) {

    $router->get('/', 'AccountController@index');
    
    $router->get('/{id}', 'AccountController@find')->int('id');

    $router->post('/', 'AccountController@save');
    
    $router->post('/{id}', 'AccountController@save')->int('id');
    
    $router->delete('/{id}', 'AccountController@delete')->int('id');

    $router->prefix('ledgers')->group(function($router) {

        $router->get('/', 'LedgerController@index');
        
        $router->get('/{id}', 'LedgerController@find')->int('id');

        $router->post('/', 'LedgerController@save');
        
        $router->post('/{id}', 'LedgerController@save')->int('id');
        
        $router->delete('/{id}', 'LedgerController@delete')->int('id');

        $router->prefix('entries')->group(function($router) {

            $router->get('/', 'EntryController@index');
            
            $router->get('/account/{id}', 'EntryController@entriesByAccount');

            $router->get('/{id}', 'EntryController@find');
            
            $router->post('/', 'EntryController@save');

            $router->post('/{id}', 'EntryController@save');

            $router->delete('/{id}', 'EntryController@delete');
        });
    });
});
