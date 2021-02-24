<?php

Route::group([
    'prefix' => 'reports',
    'middleware' => ['api', 'jwt.auth']
], function ($router) {
    $router->get(
        '',
        'ReportController@index'
    );

    $router->get(
        '/{report}',
        'ReportController@get'
    );

    $router->get(
        '/{report}/data',
        'ReportController@data'
    );

    $router->put(
        '/{report}/csv',
        'ReportController@csvWithIds'
    );

    $router->get(
        '/{report}/csv',
        'ReportController@csv'
    );
});
