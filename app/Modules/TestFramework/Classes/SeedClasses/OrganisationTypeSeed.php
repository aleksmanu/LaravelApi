<?php

namespace App\Modules\TestFramework\Classes\SeedClasses;

use App\Modules\TestFramework\Classes\BaseSeedClass;

class OrganisationTypeSeed extends BaseSeedClass
{

    /**
     * @return array
     */
    public static function generate()
    {

        $self = new static;

        $name = $self->faker->word;

        return [
            'name' => $name
        ];
    }
}
