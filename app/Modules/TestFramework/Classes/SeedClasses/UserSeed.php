<?php

namespace App\Modules\TestFramework\Classes\SeedClasses;

use App\Modules\Account\Models\Account;
use App\Modules\TestFramework\Classes\BaseSeedClass;

class UserSeed extends BaseSeedClass
{

    /**
     * @param Account|null $account
     * @return array|mixed
     */
    public static function generate(Account $account = null)
    {

        $self = new static;

        if (!$account) {
            $account = Account::where('name', '!=', 'SYSTEM')->inRandomOrder()->first();
        }

        return [
            'account_id' => $account->id,
            'first_name' => $self->faker->firstName,
            'last_name'  => $self->faker->lastName,
            'email'      => microtime(true) . $self->faker->unique()->safeEmail,
            'password'   => \Illuminate\Support\Facades\Hash::make($self->faker->word),
        ];
    }
}
