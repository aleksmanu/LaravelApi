<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your module. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

use Illuminate\Support\Facades\Auth;

Route::get('mail', function () {
    Auth::login(\App\Modules\Auth\Models\User::first());
    return new App\Mail\AcquisitionTaskCompletedAdminMail(request()->user());
});