<?php

namespace App\Modules\Lease\Http\Requests\TransactionTypes;

use App\Modules\Lease\Models\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionTypeUpdateRequest extends FormRequest
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
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique(TransactionType::getTableName())->ignore($this->route('transactionType'))
            ],
            'code' => [
                'sometimes',
                'numeric',
                'digits_between:1,10',
                Rule::unique(TransactionType::getTableName())->ignore($this->route('transactionType'))
            ],
        ];
    }
}
