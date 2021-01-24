<?php

/**
 * @var $app Alpha\Framework\Foundation\Application
 */

$app->rest->get('/reports', 'ReportController@index');
$app->rest->get('/dashboard', 'DashboardController@index');
$app->rest->get('/chart/data', 'DashboardController@getChartData');

$app->rest->prefix('accounts')->group(function($app) {

    $app->rest->get('/', 'AccountController@index');
    
    $app->rest->get('/{id}', 'AccountController@find')->int('id');

    $app->rest->post('/', 'AccountController@save');
    
    $app->rest->post('/{id}', 'AccountController@save')->int('id');
    
    $app->rest->delete('/{id}', 'AccountController@delete')->int('id');

    $app->rest->prefix('ledgers')->group(function($app) {

        $app->rest->get('/', 'LedgerController@index');
        
        $app->rest->get('/{id}', 'LedgerController@find')->int('id');

        $app->rest->post('/', 'LedgerController@save');
        
        $app->rest->post('/{id}', 'LedgerController@save')->int('id');
        
        $app->rest->delete('/{id}', 'LedgerController@delete')->int('id');

        $app->rest->prefix('entries')->group(function($app) {

            $app->rest->get('/', 'EntryController@index');
            
            $app->rest->get('/account/{id}', 'EntryController@entriesByAccount');

            $app->rest->get('/{id}', 'EntryController@find');
            
            $app->rest->post('/', 'EntryController@save');

            $app->rest->post('/{id}', 'EntryController@save');

            $app->rest->delete('/{id}', 'EntryController@delete');
        });
    });
});
