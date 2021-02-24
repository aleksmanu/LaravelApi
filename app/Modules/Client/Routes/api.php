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
    'prefix' => 'client',
    'middleware' => ['api', 'jwt.auth']
], function ($router) {
    /**
     * AUTH GUARDED GROUP -BEGIN-
     */
    /**
     * PORTFOLIO ROUTES
     */
    $router->group([
        'prefix' => 'portfolios'
    ], function ($router) {

        $router->get(
            '/',
            '\App\Modules\Client\Http\Controllers\PortfolioController@index'
        )->middleware('can:index,' . \App\Modules\Client\Models\Portfolio::class);

        $router->get(
            '/get-editable',
            '\App\Modules\Client\Http\Controllers\PortfolioController@getEditable'
        )->middleware('can:update,' . \App\Modules\Client\Models\Portfolio::class);

        $router->get(
            'data-table',
            '\App\Modules\Client\Http\Controllers\PortfolioController@dataTable'
        )->middleware('can:index,' . \App\Modules\Client\Models\Portfolio::class);

        $router->post(
            '/',
            '\App\Modules\Client\Http\Controllers\PortfolioController@store'
        )->middleware('can:create,' . \App\Modules\Client\Models\Portfolio::class);

        $router->post(
            '/{portfolio}/note',
            '\App\Modules\Client\Http\Controllers\PortfolioController@storeNote'
        )->middleware('can:create,' . \App\Modules\Client\Models\Portfolio::class);

        $router->get(
            'summary',
            '\App\Modules\Client\Http\Controllers\PortfolioController@summarize'
        )->middleware('can:index,' . \App\Modules\Client\Models\Portfolio::class);

        $router->get(
            '/edit-audit-trail/{portfolio_id}',
            '\App\Modules\Client\Http\Controllers\PortfolioController@getEditAuditTrail'
        )->middleware('can:index,' . \App\Modules\Client\Models\Portfolio::class);

        $router->get(
            '/{portfolio}',
            '\App\Modules\Client\Http\Controllers\PortfolioController@show'
        )->middleware('can:read,' . \App\Modules\Client\Models\Portfolio::class);

        $router->match(
            ['put', 'patch'],
            '/{portfolio}',
            '\App\Modules\Client\Http\Controllers\PortfolioController@update'
        )->middleware('can:update,' . \App\Modules\Client\Models\Portfolio::class);

        $router->delete(
            '/{portfolio}',
            '\App\Modules\Client\Http\Controllers\PortfolioController@destroy'
        )->middleware('can:delete,' . \App\Modules\Client\Models\Portfolio::class);

        $router->get(
            '/flag/{portfolio}',
            '\App\Modules\Client\Http\Controllers\PortfolioController@flag'
        )->middleware('can:update,' . \App\Modules\Client\Models\Portfolio::class);

        $router->post(
            '/find',
            '\App\Modules\Client\Http\Controllers\PortfolioController@find'
        )->middleware('can:index,' . \App\Modules\Client\Models\Portfolio::class);
    });


    /**
     * CLIENT-ACCOUNT ROUTES
     */
    $router->group([
        'prefix' => 'client-accounts'
    ], function ($router) {
        $controllerClassPath = '\\' . \App\Modules\Client\Http\Controllers\ClientAccountController::class;
        $modelClassPath = '\\' . \App\Modules\Client\Models\ClientAccount::class;

        $router->get(
            '/',
            '\App\Modules\Client\Http\Controllers\ClientAccountController@index'
        )->middleware('can:index,' . \App\Modules\Client\Models\ClientAccount::class);

        $router->get(
            '/data-table',
            '\App\Modules\Client\Http\Controllers\ClientAccountController@dataTable'
        )->middleware('can:index,' . \App\Modules\Client\Models\ClientAccount::class);

        $router->get(
            '/get-editable',
            '\App\Modules\Client\Http\Controllers\ClientAccountController@getEditable'
        )->middleware('can:update,' . \App\Modules\Client\Models\ClientAccount::class);

        $router->post(
            '/find',
            '\App\Modules\Client\Http\Controllers\ClientAccountController@find'
        )->middleware('can:index,' . \App\Modules\Client\Models\ClientAccount::class);

        $router->post(
            '/',
            '\App\Modules\Client\Http\Controllers\ClientAccountController@store'
        )->middleware('can:create,' . \App\Modules\Client\Models\ClientAccount::class);

        $router->post(
            '/{clientAccount}/note',
            '\App\Modules\Client\Http\Controllers\ClientAccountController@storeNote'
        )->middleware('can:create,' . \App\Modules\Client\Models\ClientAccount::class);

        $router->get(
            'summary',
            '\App\Modules\Client\Http\Controllers\ClientAccountController@summarize'
        )->middleware('can:index,' . \App\Modules\Client\Models\ClientAccount::class);

        $router->get(
            '/{clientAccount}',
            '\App\Modules\Client\Http\Controllers\ClientAccountController@show'
        )->middleware('can:read,' . \App\Modules\Client\Models\ClientAccount::class);

        $router->match(
            ['put', 'patch'],
            '/{clientAccount}',
            '\App\Modules\Client\Http\Controllers\ClientAccountController@update'
        )->middleware('can:update,' . \App\Modules\Client\Models\ClientAccount::class);

        $router->delete(
            '/{clientAccount}',
            '\App\Modules\Client\Http\Controllers\ClientAccountController@destroy'
        )->middleware('can:delete,' . \App\Modules\Client\Models\ClientAccount::class);

        $router->get(
            '/edit-audit-trail/{clientAccount}',
            '\App\Modules\Client\Http\Controllers\ClientAccountController@getEditAuditTrail'
        )->middleware('can:read,' . \App\Modules\Client\Models\ClientAccount::class);

        $router->get(
            '/flag/{clientAccount}',
            '\App\Modules\Client\Http\Controllers\ClientAccountController@flag'
        )->middleware('can:update,' . \App\Modules\Client\Models\ClientAccount::class);
        
        $router->get(
            '/{assetId}/upload',
            $controllerClassPath . "@indexAttachments"
        )->middleware('can:index,' . $modelClassPath);

        $router->get(
            '/upload/{attachmentId}',
            $controllerClassPath . "@showAttachment"
        )->middleware('can:index,' . $modelClassPath);

        $router->post(
            '/{assetId}/upload/',
            $controllerClassPath . "@storeAttachment"
        )->middleware('can:create,' . $modelClassPath);

        $router->delete(
            '/upload/{attachmentId}',
            $controllerClassPath . "@destroyAttachment"
        )->middleware('can:delete,' . $modelClassPath);

        $router->get(
            '/upload/{attachmentId}/archive',
            $controllerClassPath . "@archiveAttachment"
        )->middleware('can:update,' . $modelClassPath);

        $router->get(
            '/upload/{attachmentId}/unarchive',
            $controllerClassPath . "@unArchiveAttachment"
        )->middleware('can:update,' . $modelClassPath);
    });


    /**
     * CLIENT-ACCOUNT-STATUS ROUTES
     */
    $router->group([
        'prefix' => 'client-account-statuses'
    ], function ($router) {

        $router->get(
            '/',
            '\App\Modules\Client\Http\Controllers\ClientAccountStatusController@index'
        )->middleware('can:index,' . \App\Modules\Client\Models\ClientAccountStatus::class);

        $router->post(
            '/',
            '\App\Modules\Client\Http\Controllers\ClientAccountStatusController@store'
        )->middleware('can:create,' . \App\Modules\Client\Models\ClientAccountStatus::class);

        $router->get(
            '/{clientAccountStatus}',
            '\App\Modules\Client\Http\Controllers\ClientAccountStatusController@show'
        )->middleware('can:read,' . \App\Modules\Client\Models\ClientAccountStatus::class);

        $router->match(
            ['put', 'patch'],
            '/{clientAccountStatus}',
            '\App\Modules\Client\Http\Controllers\ClientAccountStatusController@update'
        )->middleware('can:update,' . \App\Modules\Client\Models\ClientAccountStatus::class);

        $router->delete(
            '/{clientAccountStatus}',
            '\App\Modules\Client\Http\Controllers\ClientAccountStatusController@destroy'
        )->middleware('can:delete,' . \App\Modules\Client\Models\ClientAccountStatus::class);
    });


    /**
     * ORGANISATION-TYPE ROUTES
     */
    $router->group([
        'prefix' => 'organisation-types'
    ], function ($router) {

        $router->get(
            '/',
            '\App\Modules\Client\Http\Controllers\OrganisationTypeController@index'
        )->middleware('can:index,' . \App\Modules\Client\Models\OrganisationType::class);

        $router->post(
            '/',
            '\App\Modules\Client\Http\Controllers\OrganisationTypeController@store'
        )->middleware('can:create,' . \App\Modules\Client\Models\OrganisationType::class);

        $router->get(
            '/{organisationType}',
            '\App\Modules\Client\Http\Controllers\OrganisationTypeController@show'
        )->middleware('can:read,' . \App\Modules\Client\Models\OrganisationType::class);

        $router->match(
            ['put', 'patch'],
            '/{organisationType}',
            '\App\Modules\Client\Http\Controllers\OrganisationTypeController@update'
        )->middleware('can:update,' . \App\Modules\Client\Models\OrganisationType::class);

        $router->delete(
            '/{organisationType}',
            '\App\Modules\Client\Http\Controllers\OrganisationTypeController@destroy'
        )->middleware('can:delete,' . \App\Modules\Client\Models\OrganisationType::class);
    });


/**
 * AUTH GUARDED GROUP -END-
 */
});
