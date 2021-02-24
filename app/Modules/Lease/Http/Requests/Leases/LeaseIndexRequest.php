<?php


namespace App\Modules\Lease\Http\Requests\Leases;

use App\Modules\Property\Models\Property;
use Illuminate\Foundation\Http\FormRequest;

class LeaseIndexRequest extends FormRequest
{
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
            'property_id' => 'sometimes|integer|exists:' . Property::getTableName() . ',id'
        ];
    }
}
