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
    'middleware' => ['api'],
    'prefix'     => 'auth'
], function ($router) {
    /**
     * UNATHORISED ROUTES
     */
    $router->post('login', '\App\Modules\Auth\Http\Controllers\AuthController@login');

    /**
     * AUTHORISED ROUTES
     */
    $router->group([
        'middleware' => ['jwt.auth']
    ], function ($router) {
        $router->post('logout', '\App\Modules\Auth\Http\Controllers\AuthController@logout');
        $router->post('refresh', '\App\Modules\Auth\Http\Controllers\AuthController@refresh');
        $router->post('me', '\App\Modules\Auth\Http\Controllers\AuthController@me');
        $router->get(
            'roles',
            '\App\Modules\Auth\Http\Controllers\RoleController@index'
        )->middleware('can:index,' . \App\Modules\Auth\Models\Role::class);

        $router->get(
            'roles-external',
            '\App\Modules\Auth\Http\Controllers\RoleController@indexExternal'
        )->middleware('can:index,' . \App\Modules\Auth\Models\Role::class);
        $router->get(
            'roles-internal',
            '\App\Modules\Auth\Http\Controllers\RoleController@indexInternal'
        )->middleware('can:index,' . \App\Modules\Auth\Models\Role::class);

          /**
           * USER routes
           */
        $router->group([
            'prefix' => 'users'
        ], function ($router) {
            $router->get(
                '/',
                '\App\Modules\Auth\Http\Controllers\UserController@index'
            )->middleware('can:index,' . \App\Modules\Auth\Models\User::class);

            $router->get(
                'only-client-users-filtered',
                '\App\Modules\Auth\Http\Controllers\UserController@onlyClientUsersFiltered'
            )->middleware('can:index,' . \App\Modules\Auth\Models\User::class);
                    
            $router->get(
                'get-users-by-role',
                '\App\Modules\Auth\Http\Controllers\UserController@getUsersByRole'
            )->middleware('can:index,' . \App\Modules\Auth\Models\User::class);

            $router->get(
                'data-table',
                '\App\Modules\Auth\Http\Controllers\UserController@datatable'
            )->middleware('can:index,' . \App\Modules\Auth\Models\User::class);

            $router->post(
                '/',
                '\App\Modules\Auth\Http\Controllers\UserController@store'
            )->middleware('can:create,' . \App\Modules\Auth\Models\User::class);

            $router->get(
                '/{user}',
                '\App\Modules\Auth\Http\Controllers\UserController@show'
            )->middleware('can:read,' . \App\Modules\Auth\Models\User::class);

            $router->match(
                ['put', 'patch'],
                '/{user}',
                '\App\Modules\Auth\Http\Controllers\UserController@update'
            )->middleware('can:update,' . \App\Modules\Auth\Models\User::class);

            $router->delete(
                '/{user}',
                '\App\Modules\Auth\Http\Controllers\UserController@destroy'
            )->middleware('can:delete,' . \App\Modules\Auth\Models\User::class);

            $router->get(
                '/{user}/restore',
                '\App\Modules\Auth\Http\Controllers\UserController@restore'
            )->middleware('can:create,' . \App\Modules\Auth\Models\User::class);
        });
    });
});
