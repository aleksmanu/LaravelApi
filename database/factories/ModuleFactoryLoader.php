<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

/*
 * Initialises module nested factories
 */

foreach(Module::enabled() as $active_module){
    $factory->load('app/Modules/' . $active_module['name'] . '/Database/Factories');
}