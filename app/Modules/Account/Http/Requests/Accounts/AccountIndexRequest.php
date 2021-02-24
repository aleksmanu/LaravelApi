<?php

namespace App\Modules\Account\Http\Requests\Accounts;

use App\Modules\Account\Models\Account;
use App\Modules\Account\Models\AccountType;
use Illuminate\Foundation\Http\FormRequest;

class AccountIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \Bouncer::can('index', Account::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account_type_id' => 'nullable|exists:' . AccountType::getTableName() . ',id'
        ];
    }
}
