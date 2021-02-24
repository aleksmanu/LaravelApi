<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/6/18
 * Time: 11:25 AM
 */

namespace App\Modules\Auth\Http\Requests\Rules;

use App\Modules\Common\Classes\Abstracts\RuleComposer;
use App\Rules\ExistingAccount;
use App\Rules\ExistingRole;

class UserRules extends RuleComposer
{
    public static function getBaseRules()
    {
        return [
            'account_id'    => ['integer', 'exists:accounts,id'],
            'first_name'    => ['string', 'max:255'],
            'last_name'     => ['string', 'max:255'],
            'email'         => ['string', 'max:255', 'unique:users', 'email'],
            'password'      => ['string', 'max:255'],
            'role'          => ['string', 'max:150', new ExistingRole],
        ];
    }

    public static function getCreateRules()
    {
        return self::appendRules(self::getBaseRules(), [
            'account_id' => ['required'],
            'first_name' => ['required'],
            'last_name'  => ['required'],
            'email'      => ['required'],
            'password'   => ['required'],
            'role'       => ['required'],
        ]);
    }

    public static function getPatchRules()
    {
        return self::appendRules(self::getBaseRules(), [
            'account_id' => ['nullable'],
            'first_name' => ['nullable'],
            'last_name'  => ['nullable'],
            'email'      => ['nullable'],
            'password'   => ['nullable'],
            'role'       => ['nullable'],
        ]);
    }
}
