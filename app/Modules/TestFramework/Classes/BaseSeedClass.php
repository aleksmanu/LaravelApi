<?php

namespace App\Modules\TestFramework\Classes;

use Faker\Factory;

abstract class BaseSeedClass
{

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * BaseSeedClass constructor.
     */
    public function __construct()
    {

        $this->faker = Factory::create('en_GB');
    }
}
