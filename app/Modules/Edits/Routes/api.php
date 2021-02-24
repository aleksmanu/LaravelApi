<?php
Route::group([
   'prefix'     => 'edits',
   'middleware' => ['api', 'jwt.auth']
], function ($router) {

    /**
     * EDIT BATCH ROUTES
     */
    $router->group([
        'prefix' => 'edit-batches'
    ], function ($router) {
        $router->get(
            '/data-table',
            '\App\Modules\Edits\Http\Controllers\EditBatchController@datatable'
        )->middleware('can:index,' . \App\Modules\Edits\Models\EditBatch::class);

        $router->post(
            '/submit/{edit_batch_id}',
            '\App\Modules\Edits\Http\Controllers\EditBatchController@submit'
        )->middleware('can:update,'. \App\Modules\Edits\Models\EditBatch::class);

        $router->get(
            '/get-edits/{edit_batch_id}',
            '\App\Modules\Edits\Http\Controllers\EditBatchController@getEdits'
        )->middleware('can:index,' . \App\Modules\Edits\Models\Edit::class);
    });

    /**
     * EDIT BATCH TYPES ROUTES
     */
    $router->group([
        'prefix' => 'edit-batch-types'
    ], function ($router) {
        $router->get(
            '/',
            '\App\Modules\Edits\Http\Controllers\EditBatchTypeController@index'
        )->middleware('can:index,' . \App\Modules\Edits\Models\EditBatchType::class);
    });

    /**
     * EDIT STATUS ROUTES
     */
    $router->group([
        'prefix' => 'edit-statuses'
    ], function ($router) {
        $router->get(
            '/',
            '\App\Modules\Edits\Http\Controllers\EditStatusController@index'
        )->middleware('can:index,' . \App\Modules\Edits\Models\EditStatus::class);
    });

    /**
     * EDIT ROUTES
     */
    $router->group([
        'prefix' => 'edits'
    ], function ($router) {
        $router->post(
            '/update-edits',
            '\App\Modules\Edits\Http\Controllers\EditController@updateEdits'
        )->middleware('can:update,' . \App\Modules\Edits\Models\Edit::class);
    });
});
