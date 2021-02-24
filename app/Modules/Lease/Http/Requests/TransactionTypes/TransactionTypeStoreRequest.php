<?php

namespace App\Modules\Lease\Http\Requests\TransactionTypes;

use App\Modules\Lease\Models\TransactionType;
use Illuminate\Foundation\Http\FormRequest;

class TransactionTypeStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:' . TransactionType::getTableName(),
            'code' => 'required|numeric|digits_between:1,10|unique:' . TransactionType::getTableName(),
        ];
    }
}
