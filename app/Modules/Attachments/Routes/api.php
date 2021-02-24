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
    'prefix' => 'attachments',
    'middleware' => ['api', 'jwt.auth']
], function ($router) {
    Route::get('categories', '\App\Modules\Attachments\Http\Controllers\CategoryController@index')
        ->middleware('can:index,' . \App\Modules\Attachments\Models\DocumentCategory::class);
});


Route::group([
    'prefix' => 'documents',
    'middleware' => ['api', 'jwt.auth']
], function ($router) {
    Route::get('categories', '\App\Modules\Attachments\Http\Controllers\DocumentCategoryController@index')
        ->middleware('can:index,' . \App\Modules\Attachments\Models\DocumentCategory::class);

    Route::get('types', '\App\Modules\Attachments\Http\Controllers\DocumentTypeController@index')
        ->middleware('can:index,' . \App\Modules\Attachments\Models\DocumentType::class);
    Route::post('types', '\App\Modules\Attachments\Http\Controllers\DocumentTypeController@store')
        ->middleware('can:create,' . \App\Modules\Attachments\Models\DocumentType::class);
    Route::post('types/{documentType}', '\App\Modules\Attachments\Http\Controllers\DocumentTypeController@patch')
        ->middleware('can:update,' . \App\Modules\Attachments\Models\DocumentType::class);

    Route::get('levels', '\App\Modules\Attachments\Http\Controllers\DocumentLevelController@index')
        ->middleware('can:index,' . \App\Modules\Attachments\Models\DocumentLevel::class);


    Route::post('/{document}', '\App\Modules\Attachments\Http\Controllers\DocumentController@patch')
        ->middleware('can:update,' . \App\Modules\Attachments\Models\DocumentLevel::class);
});
