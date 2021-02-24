<?php

use Illuminate\Http\Request;

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
    'prefix' => 'work-order',
    'middleware' => ['api', 'jwt.auth']
], function ($router) {
    Route::group(['prefix' => 'quotes'], function ($router) {
        Route::get(
            '/',
            '\App\Modules\Workorder\Http\Controllers\QuoteController@index'
        )->middleware('can:index,' . \App\Modules\Workorder\Models\Quote::class);

        Route::get(
            '/index-extended',
            '\App\Modules\Workorder\Http\Controllers\QuoteController@indexExtended'
        )->middleware('can:index,' . \App\Modules\Workorder\Models\Quote::class);

        Route::get(
            '/data-table',
            '\App\Modules\Workorder\Http\Controllers\QuoteController@datatable'
        )->middleware('can:index,' . \App\Modules\Workorder\Models\Quote::class);

        Route::get(
            '/{quote}',
            '\App\Modules\Workorder\Http\Controllers\QuoteController@show'
        )->middleware('can:read,' . \App\Modules\Workorder\Models\Quote::class);

        Route::post(
            '/',
            '\App\Modules\Workorder\Http\Controllers\QuoteController@store'
        )->middleware('can:create,' . \App\Modules\Workorder\Models\Quote::class);

        Route::match(
            ['put', 'patch'],
            '/{quote}',
            '\App\Modules\Workorder\Http\Controllers\QuoteController@update'
        )->middleware('can:update,' . \App\Modules\Workorder\Models\Quote::class);

        Route::delete(
            '/{quote}',
            '\App\Modules\Workorder\Http\Controllers\QuoteController@destroy'
        )->middleware('can:delete,' . \App\Modules\Workorder\Models\Quote::class);

        Route::post(
            '/{quote}/accept',
            '\App\Modules\Workorder\Http\Controllers\QuoteController@accept'
        )->middleware('can:update,' . \App\Modules\Workorder\Models\Quote::class);

        Route::post(
            '/store-auto-accepted',
            '\App\Modules\Workorder\Http\Controllers\QuoteController@storeAutoAccepted'
        )->middleware('can:create,' . \App\Modules\Workorder\Models\Quote::class)
         ->middleware('can:create,' . \App\Modules\Workorder\Models\WorkOrder::class);

        Route::get(
            '/{quote}/hotPDF',
            '\App\Modules\Workorder\Http\Controllers\QuoteController@toHotPDF'
        )->middleware('can:read,' . \App\Modules\Workorder\Models\Quote::class);
    });


    Route::group(['prefix' => 'work-orders'], function ($router) {
        Route::get(
            '/',
            '\App\Modules\Workorder\Http\Controllers\WorkOrderController@index'
        )->middleware('can:index,' . \App\Modules\Workorder\Models\WorkOrder::class);

        Route::get(
            '/data-table',
            '\App\Modules\Workorder\Http\Controllers\WorkOrderController@datatable'
        )->middleware('can:index,' . \App\Modules\Workorder\Models\WorkOrder::class);

        Route::get(
            '/{work_order}',
            '\App\Modules\Workorder\Http\Controllers\WorkOrderController@show'
        )->middleware('can:read,' . \App\Modules\Workorder\Models\WorkOrder::class);

        Route::post(
            '/',
            '\App\Modules\Workorder\Http\Controllers\WorkOrderController@store'
        )->middleware('can:create,' . \App\Modules\Workorder\Models\WorkOrder::class);

        Route::match(
            ['put', 'patch'],
            '/{work_order}',
            '\App\Modules\Workorder\Http\Controllers\WorkOrderController@update'
        )->middleware('can:update,' . \App\Modules\Workorder\Models\WorkOrder::class);

        Route::post(
            '/{work_order}/complete',
            '\App\Modules\Workorder\Http\Controllers\WorkOrderController@complete'
        )->middleware('can:update,' . \App\Modules\Workorder\Models\WorkOrder::class);

        Route::post(
            '/{work_order}/pay',
            '\App\Modules\Workorder\Http\Controllers\WorkOrderController@pay'
        )->middleware('can:update,' . \App\Modules\Workorder\Models\WorkOrder::class);

        Route::delete(
            '/{work_order}',
            '\App\Modules\Workorder\Http\Controllers\WorkOrderController@destroy'
        )->middleware('can:delete,' . \App\Modules\Workorder\Models\WorkOrder::class);

        Route::get(
            '/{work_order}/hotPDF',
            '\App\Modules\Workorder\Http\Controllers\WorkOrderController@toHotPDF'
        )->middleware('can:read,' . \App\Modules\Workorder\Models\WorkOrder::class);
    });


    Route::group(['prefix' => 'suppliers'], function ($router) {
        Route::get(
            '/',
            '\App\Modules\Workorder\Http\Controllers\SupplierController@index'
        )->middleware('can:index,' . \App\Modules\Workorder\Models\Supplier::class);
    });


    Route::group(['prefix' => 'expenditure-types'], function ($router) {
        Route::get(
            '/',
            '\App\Modules\Workorder\Http\Controllers\ExpenditureTypeController@index'
        )->middleware('can:index,' . \App\Modules\Workorder\Models\ExpenditureType::class);
    });

    Route::get(
        '/stats',
        '\App\Modules\Workorder\Http\Controllers\StatisticsController@index'
    )->middleware('can:index,' . \App\Modules\Workorder\Models\WorkOrder::class);
});
