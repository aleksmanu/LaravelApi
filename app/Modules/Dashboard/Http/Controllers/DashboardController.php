<?php

namespace App\Modules\Dashboard\Http\Controllers;

use App\Http\Controllers\Controller;

abstract class DashboardController extends Controller
{

    /**
     * @param $class
     * @return mixed
     */
    public function makeClass($class)
    {
        return \App::make($class);
    }
}
