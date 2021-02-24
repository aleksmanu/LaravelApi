<?php

namespace App\Modules\Acquisition\Http\Requests;

use App\Modules\Account\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class AcquisitionCreateRequest extends FormRequest
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
            'name' => 'required|string',
            'account_id' => 'required|integer|exists:' . Account::getTableName() . ',id',
            'users' => 'required',
            'sites' => 'nullable',
            'popAreas' => 'required',
            'commence_at' => 'sometimes|date'
        ];
    }
}
