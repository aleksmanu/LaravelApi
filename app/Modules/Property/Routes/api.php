<?php

use App\Modules\Property\Http\Controllers\PropertyController;
use App\Modules\Property\Http\Controllers\UnitController;
use App\Modules\Property\Models\Unit;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\LocationType;
use App\Modules\Property\Models\Partner;
use App\Modules\Property\Models\MeasurementUnit;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Property\Models\PropertyCategory;
use App\Modules\Property\Models\PropertyStatus;
use App\Modules\Property\Models\PropertyUse;
use App\Modules\Property\Models\PropertyTenure;
use App\Modules\Property\Models\StopPosting;

Route::group([
    'prefix' => 'property',
    'middleware' => ['api', 'jwt.auth']
], function ($router) {
    $router->post('/resolve-postcode', '\App\Modules\Property\Http\Controllers\PropertyController@resolvePostcode');

    /**
     * LOCATION-TYPE ROUTES
     */
    $router->group([
        'prefix' => 'location-types'
    ], function ($router) {

        $router->get('/', '\App\Modules\Property\Http\Controllers\LocationTypeController@index')
            ->middleware('can:index,' . LocationType::class);

        $router->post('/', '\App\Modules\Property\Http\Controllers\LocationTypeController@store')
            ->middleware('can:create,' . LocationType::class);

        $router->get('/{locationType}', '\App\Modules\Property\Http\Controllers\LocationTypeController@show')
            ->middleware('can:read,' . LocationType::class);

        $router->match(
            ['put', 'patch'],
            '/{locationType}',
            '\App\Modules\Property\Http\Controllers\LocationTypeController@update'
        )->middleware('can:update,' . LocationType::class);

        $router->delete('/{locationType}', '\App\Modules\Property\Http\Controllers\LocationTypeController@destroy')
            ->middleware('can:delete,' . LocationType::class);
    });


    /**
     * MEASUREMENT-UNIT ROUTES
     */
    $router->group([
        'prefix' => 'measurement-units'
    ], function ($router) {

        $router->get('/', '\App\Modules\Property\Http\Controllers\MeasurementUnitController@index')
            ->middleware('can:index,' . MeasurementUnit::class);

        $router->post('/', '\App\Modules\Property\Http\Controllers\MeasurementUnitController@store')
            ->middleware('can:create,' . MeasurementUnit::class);

        $router->get('/{measurementUnit}', '\App\Modules\Property\Http\Controllers\MeasurementUnitController@show')
            ->middleware('can:read,' . MeasurementUnit::class);

        $router->match(
            ['put', 'patch'],
            '/{measurementUnit}',
            '\App\Modules\Property\Http\Controllers\MeasurementUnitController@update'
        )->middleware('can:update,' . MeasurementUnit::class);

        $router->delete(
            '/{measurementUnit}',
            '\App\Modules\Property\Http\Controllers\MeasurementUnitController@destroy'
        )->middleware('can:delete,' . MeasurementUnit::class);
    });

    /**
     * PARTNER ROUTES
     */
    $router->group([
        'prefix' => 'partners'
    ], function ($router) {
        $router->get('/', '\App\Modules\Property\Http\Controllers\PartnerController@index')
            ->middleware('can:index,' . Partner::class);

        $router->get('/{measurementUnit}', '\App\Modules\Property\Http\Controllers\PartnerController@show')
            ->middleware('can:read,' . Partner::class);

        $router->get(
            '/get-partners-with-props',
            '\App\Modules\Property\Http\Controllers\PartnerController@indexWithProps'
        )->middleware('can:index,' . Partner::class);

        $router->get(
            '/{measurementUnit}/get-partner-with-props',
            '\App\Modules\Property\Http\Controllers\PartnerController@showWithProps'
        )->middleware('can:read,' . Partner::class);
    });


    /**
     * PROPERTY ROUTES
     */
    $router->group([
        'prefix' => 'properties'
    ], function ($router) {
        $controllerClassPath = '\\' . PropertyController::class;
        $modelClassPath = '\\' . Property::class;

        $router->get('/', '\App\Modules\Property\Http\Controllers\PropertyController@index')
            ->middleware('can:index,' . Property::class);

        $router->get('/get-editable', '\App\Modules\Property\Http\Controllers\PropertyController@getEditable')
            ->middleware('can:update,' . Property::class);

        $router->get('data-table', '\App\Modules\Property\Http\Controllers\PropertyController@dataTable')
            ->middleware('can:index,' . Property::class);

        $router->post('/', '\App\Modules\Property\Http\Controllers\PropertyController@store')
            ->middleware('can:create,' . Property::class);

        $router->post('/{property}/note', '\App\Modules\Property\Http\Controllers\PropertyController@storeNote')
            ->middleware('can:create,' . Property::class);

        $router->get('summary', '\App\Modules\Property\Http\Controllers\PropertyController@summarize')
            ->middleware('can:index,' . Property::class);

        $router->get('/{property}', '\App\Modules\Property\Http\Controllers\PropertyController@show')
            ->middleware('can:read,' . Property::class);

        $router->get(
            '/edit-audit-trail/{property_id}',
            '\App\Modules\Property\Http\Controllers\PropertyController@getEditAuditTrail'
        )->middleware('can:index,' . Property::class);

        $router->match(
            ['put', 'patch'],
            '/{property}',
            '\App\Modules\Property\Http\Controllers\PropertyController@update'
        )->middleware('can:update,' . Property::class);

        $router->delete('/{property}', '\App\Modules\Property\Http\Controllers\PropertyController@destroy')
            ->middleware('can:delete,' . Property::class);

        $router->get('/flag/{property}', '\App\Modules\Property\Http\Controllers\PropertyController@flag')
            ->middleware('can:update,' . Property::class);

        $router->get('/{assetId}/upload', $controllerClassPath . "@indexAttachments")
            ->middleware('can:index,' . $modelClassPath);

        $router->get('/{assetId}/uploadPhotos', $controllerClassPath . "@indexPhotos")
            ->middleware('can:index,' . $modelClassPath);

        $router->get('/upload/{attachmentId}', $controllerClassPath . "@showAttachment")
            ->middleware('can:index,' . $modelClassPath);

        $router->post('/{assetId}/upload', $controllerClassPath . "@storeAttachment")
            ->middleware('can:create,' . $modelClassPath);

        $router->delete('/upload/{attachmentId}', $controllerClassPath . "@destroyAttachment")
            ->middleware('can:delete,' . $modelClassPath);

        $router->get('/upload/{attachmentId}/archive', $controllerClassPath . "@archiveAttachment")
            ->middleware('can:update,' . $modelClassPath);

        $router->get('/upload/{attachmentId}/unarchive', $controllerClassPath . "@unArchiveAttachment")
            ->middleware('can:update,' . $modelClassPath);

        $router->post('/find', '\App\Modules\Property\Http\Controllers\PropertyController@find')
            ->middleware('can:index,' . Property::class);

        $router->get('/{assetId}/note', '\App\Modules\Property\Http\Controllers\PropertyController@getNotes')
            ->middleware('can:read,' . Property::class);
        $router->post('/{assetId}/note', '\App\Modules\Property\Http\Controllers\PropertyController@storeNote')
            ->middleware('can:update,' . Property::class);
    });


    /**
     * PROPERTY-MANAGER ROUTES
     */
    $router->group([
        'prefix' => 'property-managers'
    ], function ($router) {

        $router->get('/', '\App\Modules\Property\Http\Controllers\PropertyManagerController@index')
            ->middleware('can:index,' . PropertyManager::class);

        $router->get('data-table', '\App\Modules\Property\Http\Controllers\PropertyManagerController@dataTable')
            ->middleware('can:index,' . PropertyManager::class);

        // Update permission as this endpoint is only used to populate PropMng edit components
        $router->get(
            'non-manager-users',
            '\App\Modules\Property\Http\Controllers\PropertyManagerController@nonManagerUsers'
        )->middleware('can:update,' . PropertyManager::class);

        $router->post('/', '\App\Modules\Property\Http\Controllers\PropertyManagerController@store')
            ->middleware('can:create,' . PropertyManager::class);

        $router->get('/{propertyManager}', '\App\Modules\Property\Http\Controllers\PropertyManagerController@show')
            ->middleware('can:read,' . PropertyManager::class);

        $router->match(
            ['put', 'patch'],
            '/{propertyManager}',
            '\App\Modules\Property\Http\Controllers\PropertyManagerController@update'
        )->middleware('can:update,' . PropertyManager::class);

        $router->delete(
            '/{propertyManager}',
            '\App\Modules\Property\Http\Controllers\PropertyManagerController@destroy'
        )->middleware('can:delete,' . PropertyManager::class);

        $router->post('/find', '\App\Modules\Property\Http\Controllers\PropertyManagerController@find')
            ->middleware('can:index,' . PropertyManager::class);
    });


    /**
     * PROPERTY-STATUS ROUTES
     */
    $router->group([
        'prefix' => 'property-statuses'
    ], function ($router) {

        $router->get('/', '\App\Modules\Property\Http\Controllers\PropertyStatusController@index')
            ->middleware('can:index,' . PropertyStatus::class);

        $router->post('/', '\App\Modules\Property\Http\Controllers\PropertyStatusController@store')
            ->middleware('can:create,' . PropertyStatus::class);

        $router->get('/{propertyStatus}', '\App\Modules\Property\Http\Controllers\PropertyStatusController@show')
            ->middleware('can:read,' . PropertyStatus::class);

        $router->match(
            ['put', 'patch'],
            '/{propertyStatus}',
            '\App\Modules\Property\Http\Controllers\PropertyStatusController@update'
        )->middleware('can:update,' . PropertyStatus::class);

        $router->delete('/{propertyStatus}', '\App\Modules\Property\Http\Controllers\PropertyStatusController@destroy')
            ->middleware('can:delete,' . PropertyStatus::class);
    });

    /**
     * PROPERTY-CATEGORY ROUTES
     */
    $router->group([
        'prefix' => 'property-categories'
    ], function ($router) {

        $router->get('/', '\App\Modules\Property\Http\Controllers\PropertyCategoryController@index')
            ->middleware('can:index,' . PropertyCategory::class);

        $router->post('/', '\App\Modules\Property\Http\Controllers\PropertyCategoryController@store')
            ->middleware('can:create,' . PropertyCategory::class);

        $router->get('/{propertyCategory}', '\App\Modules\Property\Http\Controllers\PropertyCategoryController@show')
            ->middleware('can:read,' . PropertyCategory::class);

        $router->match(
            ['put', 'patch'],
            '/{propertyCategory}',
            '\App\Modules\Property\Http\Controllers\PropertyCategoryController@update'
        )->middleware('can:update,' . PropertyCategory::class);

        $router->delete(
            '/{propertyCategory}',
            '\App\Modules\Property\Http\Controllers\PropertyCategoryController@destroy'
        )->middleware('can:delete,' . PropertyCategory::class);
    });

    /**
     * PROPERTY-USE ROUTES
     */
    $router->group([
        'prefix' => 'property-uses'
    ], function ($router) {

        $router->get('/', '\App\Modules\Property\Http\Controllers\PropertyUseController@index')
            ->middleware('can:index,' . PropertyUse::class);

        $router->post('/', '\App\Modules\Property\Http\Controllers\PropertyUseController@store')
            ->middleware('can:create,' . PropertyUse::class);

        $router->get('/{propertyUse}', '\App\Modules\Property\Http\Controllers\PropertyUseController@show')
            ->middleware('can:read,' . PropertyUse::class);

        $router->match(
            ['put', 'patch'],
            '/{propertyUse}',
            '\App\Modules\Property\Http\Controllers\PropertyUseController@update'
        )->middleware('can:update,' . PropertyUse::class);

        $router->delete('/{propertyUse}', '\App\Modules\Property\Http\Controllers\PropertyUseController@destroy')
            ->middleware('can:delete,' . PropertyUse::class);
    });


    /**
     * PROPERTY-TENURE ROUTES
     */
    $router->group([
        'prefix' => 'property-tenures'
    ], function ($router) {

        $router->get('/', '\App\Modules\Property\Http\Controllers\PropertyTenureController@index')
            ->middleware('can:index,' . PropertyTenure::class);

        $router->post('/', '\App\Modules\Property\Http\Controllers\PropertyTenureController@store')
            ->middleware('can:create,' . PropertyTenure::class);

        $router->get('/{property_tenure}', '\App\Modules\Property\Http\Controllers\PropertyTenureController@show')
            ->middleware('can:read,' . PropertyTenure::class);

        $router->match(
            ['put', 'patch'],
            '/{property_tenure}',
            '\App\Modules\Property\Http\Controllers\PropertyTenureController@update'
        )->middleware('can:update,' . PropertyTenure::class);

        $router->delete('/{property_tenure}', '\App\Modules\Property\Http\Controllers\PropertyTenureController@destroy')
            ->middleware('can:delete,' . PropertyTenure::class);
    });

    /**
     * STOP-POSTING ROUTES
     */
    $router->group([
        'prefix' => 'stop-postings'
    ], function ($router) {

        $router->get('/', '\App\Modules\Property\Http\Controllers\StopPostingController@index')
            ->middleware('can:index,' . StopPosting::class);

        $router->post('/', '\App\Modules\Property\Http\Controllers\StopPostingController@store')
            ->middleware('can:create,' . StopPosting::class);

        $router->get('/{stopPosting}', '\App\Modules\Property\Http\Controllers\StopPostingController@show')
            ->middleware('can:read,' . StopPosting::class);

        $router->match(
            ['put', 'patch'],
            '/{stopPosting}',
            '\App\Modules\Property\Http\Controllers\StopPostingController@update'
        )->middleware('can:update,' . StopPosting::class);

        $router->delete('/{stopPosting}', '\App\Modules\Property\Http\Controllers\StopPostingController@destroy')
            ->middleware('can:delete,' . StopPosting::class);
    });


    /**
     * UNIT ROUTES
     */
    $router->group([
        'prefix' => 'units'
    ], function ($router) {
        $controllerClassPath = '\\' . UnitController::class;
        $modelClassPath = '\\' . Unit::class;

        $router->get('/', '\App\Modules\Property\Http\Controllers\UnitController@index')
            ->middleware('can:index,' . Unit::class);

        $router->get('/get-editable', '\App\Modules\Property\Http\Controllers\UnitController@getEditable')
            ->middleware('can:update,' . Unit::class);

        $router->get('/data-table', '\App\Modules\Property\Http\Controllers\UnitController@dataTable')
            ->middleware('can:index,' . Unit::class);

        $router->post('/', '\App\Modules\Property\Http\Controllers\UnitController@store')
            ->middleware('can:create,' . Unit::class);

        $router->post('/{unit}/note', '\App\Modules\Property\Http\Controllers\UnitController@storeNote')
            ->middleware('can:create,' . Unit::class);

        $router->get('summary', '\App\Modules\Property\Http\Controllers\UnitController@summarize')
            ->middleware('can:index,' . Unit::class);

        $router->get('/{unit}', '\App\Modules\Property\Http\Controllers\UnitController@show')
            ->middleware('can:read,' . Unit::class);

        $router->get(
            '/edit-audit-trail/{unit_id}',
            '\App\Modules\Property\Http\Controllers\UnitController@getEditAuditTrail'
        )->middleware('can:index,' . Unit::class);

        $router->match(['put', 'patch'], '/{unit}', '\App\Modules\Property\Http\Controllers\UnitController@update')
            ->middleware('can:update,' . Unit::class);

        $router->delete('/{unit}', '\App\Modules\Property\Http\Controllers\UnitController@destroy')
            ->middleware('can:delete,' . Unit::class);

        $router->get('/flag/{unit}', '\App\Modules\Property\Http\Controllers\UnitController@flag')
            ->middleware('can:update,' . Unit::class);

        $router->get('/{assetId}/upload', $controllerClassPath . "@indexAttachments")
            ->middleware('can:index,' . $modelClassPath);

        $router->get('/upload/{attachmentId}', $controllerClassPath . "@showAttachment")
            ->middleware('can:index,' . $modelClassPath);

        $router->post('/{assetId}/upload/', $controllerClassPath . "@storeAttachment")
            ->middleware('can:create,' . $modelClassPath);

        $router->delete('/upload/{attachmentId}', $controllerClassPath . "@destroyAttachment")
            ->middleware('can:delete,' . $modelClassPath);

        $router->get('/upload/{attachmentId}/archive', $controllerClassPath . "@archiveAttachment")
            ->middleware('can:update,' . $modelClassPath);

        $router->get('/upload/{attachmentId}/unarchive', $controllerClassPath . "@unArchiveAttachment")
            ->middleware('can:update,' . $modelClassPath);

        $router->post('/find', '\App\Modules\Property\Http\Controllers\UnitController@find')
            ->middleware('can:index,' . Unit::class);
        $router->post('/dash-find', '\App\Modules\Property\Http\Controllers\UnitController@dashFind')
            ->middleware('can:index,' . Unit::class);


        $router->get('/{assetId}/note', '\App\Modules\Property\Http\Controllers\UnitController@getNotes')
            ->middleware('can:read,' . Unit::class);
        $router->post('/{assetId}/note', '\App\Modules\Property\Http\Controllers\UnitController@storeNote')
            ->middleware('can:update,' . Unit::class);
    });
});
