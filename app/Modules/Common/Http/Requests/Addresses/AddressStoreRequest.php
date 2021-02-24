<?php

namespace App\Modules\Common\Http\Requests\Addresses;

use App\Modules\Common\Models\Country;
use App\Modules\Common\Models\County;
use Illuminate\Foundation\Http\FormRequest;

class AddressStoreRequest extends FormRequest
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
            'county_id'  => 'nullable|integer|exists:' . County::getTableName() . ',id',
            'country_id' => 'nullable|integer|exists:' . Country::getTableName() . ',id',
            'unit'       => 'nullable|string|max:255',
            'building'   => 'nullable|string|max:255',
            'number'     => 'nullable|max:5',
            'street'     => 'nullable|string|max:255',
            'estate'     => 'nullable|string|max:255',
            'suburb'     => 'nullable|string|max:255',
            'town'       => 'nullable|string|max:255',
            'postcode'   => 'nullable|string|max:10',
        ];
    }
}
