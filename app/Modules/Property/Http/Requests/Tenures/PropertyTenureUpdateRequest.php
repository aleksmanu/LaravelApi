<?php

namespace App\Modules\Property\Http\Requests\Tenures;

use App\Modules\Property\Models\PropertyTenure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyTenureUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // More granular rules can go here, return false to halt
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
                Rule::unique(PropertyTenure::getTableName())->ignore($this->route('property_tenure'))
            ],
        ];
    }
}
