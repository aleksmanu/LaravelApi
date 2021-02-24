<?php

namespace App\Modules\Auth\Http\Requests\Users;

use App\Modules\Account\Models\Account;
use App\Modules\Account\Models\AccountType;
use App\Modules\Auth\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserIndex extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Bouncer::can('index', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account_type_id' => 'nullable|exists:' . AccountType::getTableName() . ',id',
            'account_id'      => 'nullable|exists:' . Account::getTableName() .',id',
            'role_id'         => 'nullable|exists:roles,id',
        ];
    }
}
