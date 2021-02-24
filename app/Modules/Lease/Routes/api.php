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
    'prefix' => 'lease',
    'middleware' => ['api', 'jwt.auth']
], function ($router) {
    /**
     * AUTH GUARDED GROUP -BEGIN-
     */


    /**
     * BREAK-PARTY-OPTION ROUTES
     */
    $router->group([
        'prefix' => 'break-party-options'
    ], function ($router) {

        $router->get(
            '/',
            '\App\Modules\Lease\Http\Controllers\BreakPartyOptionController@index'
        )->middleware('can:index,' . \App\Modules\Lease\Models\BreakPartyOption::class);

        $router->post(
            '/',
            '\App\Modules\Lease\Http\Controllers\BreakPartyOptionController@store'
        )->middleware('can:create,' . \App\Modules\Lease\Models\BreakPartyOption::class);

        $router->get(
            '/{breakPartyOption}',
            '\App\Modules\Lease\Http\Controllers\BreakPartyOptionController@show'
        )->middleware('can:read,' . \App\Modules\Lease\Models\BreakPartyOption::class);

        $router->match(
            ['put', 'patch'],
            '/{breakPartyOption}',
            '\App\Modules\Lease\Http\Controllers\BreakPartyOptionController@update'
        )->middleware('can:update,' . \App\Modules\Lease\Models\BreakPartyOption::class);

        $router->delete(
            '/{breakPartyOption}',
            '\App\Modules\Lease\Http\Controllers\BreakPartyOptionController@destroy'
        )->middleware('can:delete,' . \App\Modules\Lease\Models\BreakPartyOption::class);
    });

    /**
     * LEASE-TYPE ROUTES
     */
    $router->group([
        'prefix' => 'lease-types'
    ], function ($router) {

        $router->get(
            '/',
            '\App\Modules\Lease\Http\Controllers\LeaseTypeController@index'
        )->middleware('can:index,' . \App\Modules\Lease\Models\LeaseType::class);

        $router->post(
            '/',
            '\App\Modules\Lease\Http\Controllers\LeaseTypeController@store'
        )->middleware('can:create,' . \App\Modules\Lease\Models\LeaseType::class);

        $router->get(
            '/{leaseType}',
            '\App\Modules\Lease\Http\Controllers\LeaseTypeController@show'
        )->middleware('can:read,' . \App\Modules\Lease\Models\LeaseType::class);

        $router->match(
            ['put', 'patch'],
            '/{leaseType}',
            '\App\Modules\Lease\Http\Controllers\LeaseTypeController@update'
        )->middleware('can:update,' . \App\Modules\Lease\Models\LeaseType::class);

        $router->delete(
            '/{leaseType}',
            '\App\Modules\Lease\Http\Controllers\LeaseTypeController@destroy'
        )->middleware('can:delete,' . \App\Modules\Lease\Models\LeaseType::class);
    });


    /**
     * RENT-FREQUENCY ROUTES
     */
    $router->group([
        'prefix' => 'rent-frequencies'
    ], function ($router) {

        $router->get(
            '/',
            '\App\Modules\Lease\Http\Controllers\RentFrequencyController@index'
        )->middleware('can:index,' . \App\Modules\Lease\Models\RentFrequency::class);

        $router->post(
            '/',
            '\App\Modules\Lease\Http\Controllers\RentFrequencyController@store'
        )->middleware('can:create,' . \App\Modules\Lease\Models\RentFrequency::class);

        $router->get(
            '/{rentFrequency}',
            '\App\Modules\Lease\Http\Controllers\RentFrequencyController@show'
        )->middleware('can:read,' . \App\Modules\Lease\Models\RentFrequency::class);

        $router->match(
            ['put', 'patch'],
            '/{rentFrequency}',
            '\App\Modules\Lease\Http\Controllers\RentFrequencyController@update'
        )->middleware('can:update,' . \App\Modules\Lease\Models\RentFrequency::class);

        $router->delete(
            '/{rentFrequency}',
            '\App\Modules\Lease\Http\Controllers\RentFrequencyController@destroy'
        )->middleware('can:delete,' . \App\Modules\Lease\Models\RentFrequency::class);
    });


    /**
     * REVIEW-TYPE ROUTES
     */
    $router->group([
        'prefix' => 'review-types'
    ], function ($router) {

        $router->get(
            '/',
            '\App\Modules\Lease\Http\Controllers\ReviewTypeController@index'
        )->middleware('can:index,' . \App\Modules\Lease\Models\ReviewType::class);

        $router->post(
            '/',
            '\App\Modules\Lease\Http\Controllers\ReviewTypeController@store'
        )->middleware('can:create,' . \App\Modules\Lease\Models\ReviewType::class);

        $router->get(
            '/{reviewType}',
            '\App\Modules\Lease\Http\Controllers\ReviewTypeController@show'
        )->middleware('can:read,' . \App\Modules\Lease\Models\ReviewType::class);

        $router->match(
            ['put', 'patch'],
            '/{reviewType}',
            '\App\Modules\Lease\Http\Controllers\ReviewTypeController@update'
        )->middleware('can:update,' . \App\Modules\Lease\Models\ReviewType::class);

        $router->delete(
            '/{reviewType}',
            '\App\Modules\Lease\Http\Controllers\ReviewTypeController@destroy'
        )->middleware('can:delete,' . \App\Modules\Lease\Models\ReviewType::class);
    });


    /**
     * TENANCY ROUTES
     */
    $router->group([
        'prefix' => 'tenants'
    ], function ($router) {
        $controllerClassPath = '\\' . App\Modules\Lease\Http\Controllers\TenantController::class;
        $modelClassPath = '\\' . App\Modules\Lease\Models\Tenant::class;

        $router->get(
            '/',
            '\App\Modules\Lease\Http\Controllers\TenantController@index'
        )->middleware('can:index,' . \App\Modules\Lease\Models\Tenant::class);

        $router->get(
            '/get-editable',
            '\App\Modules\Lease\Http\Controllers\TenantController@getEditable'
        )->middleware('can:update,' . \App\Modules\Lease\Models\Tenant::class);

        $router->post(
            '/',
            '\App\Modules\Lease\Http\Controllers\TenantController@store'
        )->middleware('can:create,' . \App\Modules\Lease\Models\Tenant::class);

        $router->post(
            '/{tenant}/note',
            '\App\Modules\Lease\Http\Controllers\TenantController@storeNote'
        )->middleware('can:create,' . \App\Modules\Lease\Models\Tenant::class);

        $router->get(
            'summary',
            '\App\Modules\Lease\Http\Controllers\TenantController@summarize'
        )->middleware('can:index,' . \App\Modules\Lease\Models\Tenant::class);

        $router->get(
            '/{tenant}',
            '\App\Modules\Lease\Http\Controllers\TenantController@show'
        )->middleware('can:read,' . \App\Modules\Lease\Models\Tenant::class);

        $router->get(
            '/edit-audit-trail/{tenant_id}',
            '\App\Modules\Lease\Http\Controllers\TenantController@getEditAuditTrail'
        )->middleware('can:read,' . \App\Modules\Lease\Models\Tenant::class);

        $router->match(
            ['put', 'patch'],
            '/{tenant}',
            '\App\Modules\Lease\Http\Controllers\TenantController@update'
        )->middleware('can:update,' . \App\Modules\Lease\Models\Tenant::class);

        $router->delete(
            '/{tenant}',
            '\App\Modules\Lease\Http\Controllers\TenantController@destroy'
        )->middleware('can:delete,' . \App\Modules\Lease\Models\Tenant::class);

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
    });


    /**
     * TENANCY-LEASE ROUTES
     */
    $router->group([
        'prefix' => 'leases'
    ], function ($router) {
        $controllerClassPath = '\\' .  App\Modules\Lease\Http\Controllers\LeaseController::class;
        $modelClassPath = '\\' . App\Modules\Lease\Models\Lease::class;

        $router->get(
            '/',
            '\App\Modules\Lease\Http\Controllers\LeaseController@index'
        )->middleware('can:index,' . \App\Modules\Lease\Models\Lease::class);

        $router->get(
            '/get-editable',
            '\App\Modules\Lease\Http\Controllers\LeaseController@getEditable'
        )->middleware('can:update,' . \App\Modules\Lease\Models\Lease::class);

        $router->get(
            '/data-table',
            '\App\Modules\Lease\Http\Controllers\LeaseController@dataTable'
        )->middleware('can:index,' . \App\Modules\Lease\Models\Lease::class);

        $router->post(
            '/',
            '\App\Modules\Lease\Http\Controllers\LeaseController@store'
        )->middleware('can:create,' . \App\Modules\Lease\Models\Lease::class);

        $router->post(
            '/{lease}/note',
            '\App\Modules\Lease\Http\Controllers\LeaseController@storeNote'
        )->middleware('can:create,' . \App\Modules\Lease\Models\Lease::class);

        $router->get(
            'summary',
            '\App\Modules\Lease\Http\Controllers\LeaseController@summarize'
        )->middleware('can:index,' . \App\Modules\Lease\Models\Lease::class);

        $router->get(
            '/edit-audit-trail/{lease_id}',
            '\App\Modules\Lease\Http\Controllers\LeaseController@getEditAuditTrail'
        )->middleware('can:read,' . \App\Modules\Lease\Models\Lease::class);

        $router->get(
            '/{lease}',
            '\App\Modules\Lease\Http\Controllers\LeaseController@show'
        )->middleware('can:read,' . \App\Modules\Lease\Models\Lease::class);

        $router->match(
            ['put', 'patch'],
            '/{lease}',
            '\App\Modules\Lease\Http\Controllers\LeaseController@update'
        )->middleware('can:update,' . \App\Modules\Lease\Models\Lease::class);

        $router->delete(
            '/{lease}',
            '\App\Modules\Lease\Http\Controllers\LeaseController@destroy'
        )->middleware('can:delete,' . \App\Modules\Lease\Models\Lease::class);

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

        $router->post(
            '/find',
            '\App\Modules\Lease\Http\Controllers\LeaseController@find'
        )->middleware('can:index,' . \App\Modules\Lease\Models\Lease::class);

        $router->get(
            '/{assetId}/note',
            '\App\Modules\Lease\Http\Controllers\LeaseController@getNotes'
        )->middleware('can:read,' . \App\Modules\Lease\Models\Lease::class);
        
        $router->post(
            '/{assetId}/note',
            '\App\Modules\Lease\Http\Controllers\LeaseController@storeNote'
        )->middleware('can:update,' . \App\Modules\Lease\Models\Lease::class);
    });


    /**
     * TENANCY-STATUS ROUTES
     */
    $router->group([
        'prefix' => 'tenant-statuses'
    ], function ($router) {

        $router->get(
            '/',
            '\App\Modules\Lease\Http\Controllers\TenantStatusController@index'
        )->middleware('can:index,' . \App\Modules\Lease\Models\TenantStatus::class);

        $router->post(
            '/',
            '\App\Modules\Lease\Http\Controllers\TenantStatusController@store'
        )->middleware('can:create,' . \App\Modules\Lease\Models\TenantStatus::class);

        $router->get(
            '/{tenantStatus}',
            '\App\Modules\Lease\Http\Controllers\TenantStatusController@show'
        )->middleware('can:read,' . \App\Modules\Lease\Models\TenantStatus::class);

        $router->match(
            ['put', 'patch'],
            '/{tenantStatus}',
            '\App\Modules\Lease\Http\Controllers\TenantStatusController@update'
        )->middleware('can:update,' . \App\Modules\Lease\Models\TenantStatus::class);

        $router->delete(
            '/{tenantStatus}',
            '\App\Modules\Lease\Http\Controllers\TenantStatusController@destroy'
        )->middleware('can:delete,' . \App\Modules\Lease\Models\TenantStatus::class);
    });


    /**
     * TRANSACTION ROUTES
     */
    $router->group([
        'prefix' => 'transactions'
    ], function ($router) {

        $router->get(
            '/',
            '\App\Modules\Lease\Http\Controllers\TransactionController@index'
        )->middleware('can:index,' . \App\Modules\Lease\Models\Transaction::class);

        $router->get(
            '/unit-data-table',
            '\App\Modules\Lease\Http\Controllers\TransactionController@unitTransactionsDataTable'
        )->middleware('can:index,' . \App\Modules\Lease\Models\Transaction::class);

        $router->post(
            '/',
            '\App\Modules\Lease\Http\Controllers\TransactionController@store'
        )->middleware('can:create,' . \App\Modules\Lease\Models\Transaction::class);

        $router->get(
            '/{transaction}',
            '\App\Modules\Lease\Http\Controllers\TransactionController@show'
        )->middleware('can:read,' . \App\Modules\Lease\Models\Transaction::class);

        $router->match(
            ['put', 'patch'],
            '/{transaction}',
            '\App\Modules\Lease\Http\Controllers\TransactionController@update'
        )->middleware('can:update,' . \App\Modules\Lease\Models\Transaction::class);

        $router->delete(
            '/{transaction}',
            '\App\Modules\Lease\Http\Controllers\TransactionController@destroy'
        )->middleware('can:delete,' . \App\Modules\Lease\Models\Transaction::class);
    });

    /**
     * TRANSACTION-TYPES ROUTES
     */
    $router->group([
        'prefix' => 'transaction-types'
    ], function ($router) {
        $router->get(
            '/',
            '\App\Modules\Lease\Http\Controllers\TransactionTypeController@index'
        )->middleware('can:index,' . \App\Modules\Lease\Models\TransactionType::class);

        $router->post(
            '/',
            '\App\Modules\Lease\Http\Controllers\TransactionTypeController@store'
        )->middleware('can:create,' . \App\Modules\Lease\Models\TransactionType::class);

        $router->get(
            '/{transactionType}',
            '\App\Modules\Lease\Http\Controllers\TransactionTypeController@show'
        )->middleware('can:read,' . \App\Modules\Lease\Models\TransactionType::class);

        $router->match(
            ['put', 'patch'],
            '/{transactionType}',
            '\App\Modules\Lease\Http\Controllers\TransactionTypeController@update'
        )->middleware('can:update,' . \App\Modules\Lease\Models\TransactionType::class);

        $router->delete(
            '/{transactionType}',
            '\App\Modules\Lease\Http\Controllers\TransactionTypeController@destroy'
        )->middleware('can:delete,' . \App\Modules\Lease\Models\TransactionType::class);
    });

    /**
     * PAID-STATUSES ROUTES
     */
    $router->group([
        'prefix' => 'paid-statuses'
    ], function ($router) {
        $router->get(
            '/',
            '\App\Modules\Lease\Http\Controllers\PaidStatusController@index'
        )->middleware('can:index,' . \App\Modules\Lease\Models\PaidStatus::class);

        $router->post(
            '/',
            '\App\Modules\Lease\Http\Controllers\PaidStatusController@store'
        )->middleware('can:create,' . \App\Modules\Lease\Models\PaidStatus::class);

        $router->get(
            '/{paidStatus}',
            '\App\Modules\Lease\Http\Controllers\PaidStatusController@show'
        )->middleware('can:read,' . \App\Modules\Lease\Models\PaidStatus::class);

        $router->match(
            ['put', 'patch'],
            '/{paidStatus}',
            '\App\Modules\Lease\Http\Controllers\PaidStatusController@update'
        )->middleware('can:update,' . \App\Modules\Lease\Models\PaidStatus::class);

        $router->delete(
            '/{paidStatus}',
            '\App\Modules\Lease\Http\Controllers\PaidStatusController@destroy'
        )->middleware('can:delete,' . \App\Modules\Lease\Models\PaidStatus::class);
    });


    /**
     * AUTH GUARDED GROUP -END-
     */
});
