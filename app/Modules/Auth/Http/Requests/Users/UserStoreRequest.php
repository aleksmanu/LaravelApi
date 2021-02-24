<?php

namespace App\Modules\Auth\Http\Requests\Users;

use App\Modules\Account\Models\Account;
use App\Modules\Auth\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account_id' => 'sometimes|nullable|exists:' . Account::getTableName() . ',id',
            'role_id'    => 'required|exists:roles,id',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|max:255|email|unique:users',
            'password'   => 'required|string|max:255',
            'user_scope' => 'required'
        ];
    }
}
