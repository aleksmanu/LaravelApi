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
    'prefix' => 'account',
    'middleware' => ['api', 'jwt.auth']
], function ($router) {

    /************** ACCOUNT TYPE ROUTES START ****************/
    $router->group([
        'prefix' => 'account-types'
    ], function ($router) {

        $router->get(
            '/',
            '\App\Modules\Account\Http\Controllers\AccountTypeController@index'
        )->middleware('can:index,' . \App\Modules\Account\Models\AccountType::class);

        $router->post(
            '/',
            '\App\Modules\Account\Http\Controllers\AccountTypeController@store'
        )->middleware('can:create,' . \App\Modules\Account\Models\AccountType::class);

        $router->get(
            '/{accountType}',
            '\App\Modules\Account\Http\Controllers\AccountTypeController@show'
        )->middleware('can:read,' . \App\Modules\Account\Models\AccountType::class);

        $router->match(
            ['put', 'patch'],
            '/{accountType}',
            '\App\Modules\Account\Http\Controllers\AccountTypeController@update'
        )->middleware('can:update,' . \App\Modules\Account\Models\AccountType::class);

        $router->delete(
            '/{accountType}',
            '\App\Modules\Account\Http\Controllers\AccountTypeController@destroy'
        )->middleware('can:delete,' . \App\Modules\Account\Models\AccountType::class);
    });
    /************** ACCOUNT TYPE ROUTES END ****************/


    /************** ACCOUNT ROUTES START ****************/
    $router->group([
        'prefix' => 'accounts'
    ], function ($router) {
        $router->get(
            '/',
            '\App\Modules\Account\Http\Controllers\AccountController@index'
        )->middleware('can:index,' . \App\Modules\Account\Models\Account::class);

        $router->post(
            '/',
            '\App\Modules\Account\Http\Controllers\AccountController@store'
        )->middleware('can:create,' . \App\Modules\Account\Models\Account::class);

        $router->get(
            '/{account}',
            '\App\Modules\Account\Http\Controllers\AccountController@show'
        )->middleware('can:read,' . \App\Modules\Account\Models\Account::class);

        $router->match(
            ['put', 'patch'],
            '/{account}',
            '\App\Modules\Account\Http\Controllers\AccountController@update'
        )->middleware('can:update,' . \App\Modules\Account\Models\Account::class);

        $router->delete(
            '/{account}',
            '\App\Modules\Account\Http\Controllers\AccountController@destroy'
        )->middleware('can:delete,' . \App\Modules\Account\Models\Account::class);
    });
    /************** ACCOUNT ROUTES END ****************/
});
