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
    'prefix' => 'acquisitions',
    'middleware' => ['api', 'jwt.auth'],
], function ($router) {
    $router->group(['prefix' => 'acquisitions'], function ($router) {
        $router->get(
            '/',
            '\App\Modules\Acquisition\Http\Controllers\AcquisitionController@index'
        )->middleware('can:index,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->get(
            '/{acquisition}',
            '\App\Modules\Acquisition\Http\Controllers\AcquisitionController@show'
        )->middleware('can:read,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->post(
            '/',
            '\App\Modules\Acquisition\Http\Controllers\AcquisitionController@create'
        )->middleware('can:read,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->match(
            ['put', 'patch'],
            '/from-template/{checklist}',
            '\App\Modules\Acquisition\Http\Controllers\AcquisitionController@createAquisitionFromTemplate'
        )->middleware('can:create,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->match(
            ['put', 'patch'],
            '/{acquisition}',
            '\App\Modules\Acquisition\Http\Controllers\AcquisitionController@update'
        )->middleware('can:update,' . \App\Modules\Acquisition\Models\Acquisition::class);
    });

    $router->group(['prefix' => 'pop_areas'], function ($router) {
        $router->delete(
            '/{pop_area}',
            '\App\Modules\Acquisition\Http\Controllers\AcquisitionController@deletePopArea'
        )->middleware('can:update,' . \App\Modules\Acquisition\Models\Acquisition::class);
    });

    $router->group(['prefix' => 'templates'], function ($router) {
        $router->get('', '\App\Modules\Acquisition\Http\Controllers\TemplateController@index')
            ->middleware('can:index,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->get('{template}', '\App\Modules\Acquisition\Http\Controllers\TemplateController@show')
            ->middleware('can:index,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->post(
            '/',
            '\App\Modules\Acquisition\Http\Controllers\TemplateController@create'
        )->middleware('can:update,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->match(
            ['put', 'patch'],
            '/{template}',
            '\App\Modules\Acquisition\Http\Controllers\TemplateController@create'
        )->middleware('can:index,' . \App\Modules\Acquisition\Models\Acquisition::class);
    });

    $router->group(['prefix' => 'checklists'], function ($router) {
        $router->get(
            '/',
            '\App\Modules\Acquisition\Http\Controllers\AcquisitionController@index'
        )->middleware('can:index,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->get(
            '/{checklist}',
            '\App\Modules\Acquisition\Http\Controllers\AcquisitionController@show'
        )->middleware('can:read,' . \App\Modules\Acquisition\Models\Acquisition::class);
    });

    $router->group(['prefix' => 'steps'], function ($router) {
        $router->match(
            ['put', 'patch'],
            '/{step}',
            '\App\Modules\Acquisition\Http\Controllers\StepController@update'
        )->middleware('can:update,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->post('/{step}/note', '\App\Modules\Acquisition\Http\Controllers\StepController@storeNote');

        // -- DOCUMENT ROUTES --
        $router->get(
            '/{assetId}/upload',
            '\App\Modules\Acquisition\Http\Controllers\StepController@indexAttachments'
        )->middleware(
            'can:index,' . \App\Modules\Acquisition\Models\Acquisition::class
        );

        $router->get(
            '/upload/{attachmentId}',
            "\App\Modules\Acquisition\Http\Controllers\StepController@showAttachment"
        )->middleware('can:index,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->post(
            '/{assetId}/upload',
            "\App\Modules\Acquisition\Http\Controllers\StepController@storeAttachment"
        )->middleware('can:create,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->post(
            '/{assetId}/upload-with-cat-name',
            "\App\Modules\Acquisition\Http\Controllers\StepController@storeAttachmentWithCat"
        )->middleware('can:create,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->delete(
            '/upload/{attachmentId}',
            "\App\Modules\Acquisition\Http\Controllers\StepController@deleteAttachment"
        )->middleware('can:delete,' . \App\Modules\Acquisition\Models\Acquisition::class);

        // -- TIMELINE ROUTES --
        $router->get(
            '{assetId}/timeline',
            "\App\Modules\Acquisition\Http\Controllers\StepController@getTimeline"
        )->middleware('can:read,' . \App\Modules\Acquisition\Models\Acquisition::class);
    });

    $router->group(['prefix' => 'sites'], function ($router) {
        $router->get(
            '/{site}',
            '\App\Modules\Acquisition\Http\Controllers\SiteController@show'
        )->middleware('can:read,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->match(
            ['put', 'patch'],
            '/{site}',
            '\App\Modules\Acquisition\Http\Controllers\SiteController@update'
        )->middleware('can:update,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->post(
            '/',
            '\App\Modules\Acquisition\Http\Controllers\SiteController@create'
        )->middleware('can:create,' . \App\Modules\Acquisition\Models\Acquisition::class);


        // -- DOCUMENT ROUTES --
        $router->get(
            '/{assetId}/upload',
            '\App\Modules\Acquisition\Http\Controllers\SiteController@indexAttachments'
        )->middleware(
            'can:index,' . \App\Modules\Acquisition\Models\Acquisition::class
        );

        $router->get(
            '/upload/{attachmentId}',
            "\App\Modules\Acquisition\Http\Controllers\SiteController@showAttachment"
        )->middleware('can:index,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->post(
            '/upload',
            "\App\Modules\Acquisition\Http\Controllers\SiteController@storeAttachment"
        )->middleware('can:create,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->post(
            '/{assetId}/upload-with-cat-name',
            "\App\Modules\Acquisition\Http\Controllers\SiteController@storeAttachmentWithCat"
        )->middleware('can:create,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->delete(
            '/upload/{attachmentId}',
            "\App\Modules\Acquisition\Http\Controllers\SiteController@deleteAttachment"
        )->middleware('can:delete,' . \App\Modules\Acquisition\Models\Acquisition::class);


        // -- TIMELINE ROUTES --
        $router->get(
            '{assetId}/timeline',
            "\App\Modules\Acquisition\Http\Controllers\SiteController@getTimeline"
        )->middleware('can:read,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->get(
            '/{assetId}/note',
            '\App\Modules\Acquisition\Http\Controllers\SiteController@getNotes'
        )->middleware('can:read,' . \App\Modules\Acquisition\Models\Acquisition::class);
        
        $router->post(
            '/{assetId}/note',
            '\App\Modules\Acquisition\Http\Controllers\SiteController@storeNote'
        )->middleware('can:update,' . \App\Modules\Acquisition\Models\Acquisition::class);
    });

    $router->group(['prefix' => 'stats'], function ($router) {
        $router->get(
            '/',
            '\App\Modules\Acquisition\Http\Controllers\StatisticsController@index'
        )->middleware('can:index,' . \App\Modules\Acquisition\Models\Acquisition::class);

        $router->get(
            '/{context}',
            '\App\Modules\Acquisition\Http\Controllers\StatisticsController@getStatistic'
        )->middleware('can:index,' . \App\Modules\Acquisition\Models\Acquisition::class);
    });

    $router->get(
        '/tasks-stats',
        '\App\Modules\Acquisition\Http\Controllers\StatisticsController@tasksIndex'
    )->middleware('can:index,' . \App\Modules\Acquisition\Models\Acquisition::class);
});
