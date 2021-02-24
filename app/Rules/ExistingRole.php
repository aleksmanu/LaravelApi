<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ExistingRole implements Rule
{
    private $roleName;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->roleName = $value;
        return \Bouncer::role()->where('name', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Role with the name '" . $this->roleName . "' does not exist.";
    }
}
