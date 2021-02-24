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
    'prefix'     => 'dashboard',
    'middleware' => ['api', 'jwt.auth']
], function ($router) {

    $router->group([
        'prefix'     => 'edit',
        'middleware' => 'can:index,' . \App\Modules\Edits\Models\EditBatch::class
    ], function ($router) {

        $router->get(
            '/client-account-review-stats',
            '\App\Modules\Dashboard\Http\Controllers\EditDashboardController@getClientAccountReviewStats'
        );
        $router->get(
            '/portfolio-review-stats',
            '\App\Modules\Dashboard\Http\Controllers\EditDashboardController@getPortfolioReviewStats'
        );
        $router->get(
            '/property-review-stats',
            '\App\Modules\Dashboard\Http\Controllers\EditDashboardController@getPropertyReviewStats'
        );
        $router->get(
            '/unit-review-stats',
            '\App\Modules\Dashboard\Http\Controllers\EditDashboardController@getUnitReviewStats'
        );
        $router->get(
            '/lease-review-stats',
            '\App\Modules\Dashboard\Http\Controllers\EditDashboardController@getLeaseReviewStats'
        );
        $router->get(
            '/tenant-review-stats',
            '\App\Modules\Dashboard\Http\Controllers\EditDashboardController@getTenantReviewStats'
        );
        $router->get(
            '/previous-week-edits',
            '\App\Modules\Dashboard\Http\Controllers\EditDashboardController@getPreviousWeekDailyEdits'
        );
        $router->get(
            '/approval-split',
            '\App\Modules\Dashboard\Http\Controllers\EditDashboardController@getEditApprovalSplit'
        );
        $router->get(
            '/reviewed-edits-total',
            '\App\Modules\Dashboard\Http\Controllers\EditDashboardController@getReviewedEditsTotal'
        );
    });
});
