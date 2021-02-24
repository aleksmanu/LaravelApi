<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your module. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'common',
    'middleware' => ['api', 'jwt.auth']
], function ($router) {
    /**
     * AUTH GUARDED GROUP -BEGIN-
     */

    /**
     * ADDRESS ROUTES
     */
    $router->group([
        'prefix' => 'addresses'
    ], function ($router) {
        $router->get('/', '\App\Modules\Common\Http\Controllers\AddressController@index')
            ->middleware('can:index,' . \App\Modules\Common\Models\Address::class);

        $router->post('/', '\App\Modules\Common\Http\Controllers\AddressController@store')
            ->middleware('can:create,' . \App\Modules\Common\Models\Address::class);

        $router->get('/{address}', '\App\Modules\Common\Http\Controllers\AddressController@show')
            ->middleware('can:read,' . \App\Modules\Common\Models\Address::class);

        $router->match(['put', 'patch'], '/{address}', '\App\Modules\Common\Http\Controllers\AddressController@update')
            ->middleware('can:update,' . \App\Modules\Common\Models\Address::class);

        $router->delete('/{address}', '\App\Modules\Common\Http\Controllers\AddressController@destroy')
            ->middleware('can:delete,' . \App\Modules\Common\Models\Address::class);
    });


    /**
     * COUNTRY ROUTES
     */
    $router->group([
        'prefix' => 'countries'
    ], function ($router) {

        $router->get('/', '\App\Modules\Common\Http\Controllers\CountryController@index')
            ->middleware('can:index,' . \App\Modules\Common\Models\Country::class);

        $router->post('/', '\App\Modules\Common\Http\Controllers\CountryController@store')
            ->middleware('can:create,' . \App\Modules\Common\Models\Country::class);

        $router->get('/{country}', '\App\Modules\Common\Http\Controllers\CountryController@show')
            ->middleware('can:read,' . \App\Modules\Common\Models\Country::class);

        $router->match(['put', 'patch'], '/{country}', '\App\Modules\Common\Http\Controllers\CountryController@update')
            ->middleware('can:update,' . \App\Modules\Common\Models\Country::class);

        $router->delete('/{country}', '\App\Modules\Common\Http\Controllers\CountryController@destroy')
            ->middleware('can:delete,' . \App\Modules\Common\Models\Country::class);
    });


    /**
     * COUNTY ROUTES
     */
    $router->group([
        'prefix' => 'counties'
    ], function ($router) {

        $router->get('/', '\App\Modules\Common\Http\Controllers\CountyController@index')
            ->middleware('can:index,' . \App\Modules\Common\Models\County::class);

        $router->post('/', '\App\Modules\Common\Http\Controllers\CountyController@store')
            ->middleware('can:create,' . \App\Modules\Common\Models\County::class);

        $router->get('/{county}', '\App\Modules\Common\Http\Controllers\CountyController@show')
            ->middleware('can:read,' . \App\Modules\Common\Models\County::class);

        $router->match(['put', 'patch'], '/{county}', '\App\Modules\Common\Http\Controllers\CountyController@update')
            ->middleware('can:update,' . \App\Modules\Common\Models\County::class);

        $router->delete('/{county}', '\App\Modules\Common\Http\Controllers\CountyController@destroy')
            ->middleware('can:delete,' . \App\Modules\Common\Models\County::class);
    });

    /**
     * DropdownAggregator ROUTES
     */
    $router->group([
        'prefix' => 'dropdown-aggregator'
    ], function ($router) {
        $router->post('/fetch', '\App\Modules\Common\Http\Controllers\DropdownAggregatorController@fetch');
    });


    /**
     * IncomeDashDataAggregator ROUTES
     */
    $router->group([
        'prefix' => 'income-dash-data-aggregator'
    ], function ($router) {
        $router->post(
            '/fetch-filter',
            '\App\Modules\Common\Http\Controllers\IncomeDashDataAggregatorController@fetchFilterOptions'
        );
        $router->post(
            '/fetch-filter-aux',
            '\App\Modules\Common\Http\Controllers\IncomeDashDataAggregatorController@fetchFilterAuxiliaryOptions'
        );
        $router->post(
            '/fetch-time-bounds',
            '\App\Modules\Common\Http\Controllers\IncomeDashDataAggregatorController@fetchTimeBounds'
        );
        $router->post(
            '/fetch-dash-data',
            '\App\Modules\Common\Http\Controllers\IncomeDashDataAggregatorController@fetchDashData'
        );
        $router->post(
            '/fetch-dash-aux-data',
            '\App\Modules\Common\Http\Controllers\IncomeDashDataAggregatorController@fetchDashAuxData'
        );
    });


    /**
     * ADDRESS ROUTES
     */
    $router->group([
        'prefix' => 'developer',
        'middleware' => 'isDeveloper'
    ], function ($router) {
        $router->get('export-users', '\App\Modules\Common\Http\Controllers\DevelopmentController@exportUsers');
        $router->post('import-users', '\App\Modules\Common\Http\Controllers\DevelopmentController@importUsers');
        $router->get('nuke-cache', '\App\Modules\Common\Http\Controllers\DevelopmentController@clearCache');
        $router->get('rebuild-finance', '\App\Modules\Common\Http\Controllers\DevelopmentController@recalculateFinanceValues');
    });


    /**
     * AUTH GUARDED GROUP -END-
     */
});
