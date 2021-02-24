<?php

namespace App\Modules\Lease\Http\Requests\PaidStatuses;

use App\Modules\Lease\Models\PaidStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaidStatusUpdateRequest extends FormRequest
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
                'required',
                'string',
                'max:255',
                Rule::unique(PaidStatus::getTableName())->ignore($this->route('paidStatus'))
            ],
        ];
    }
}
