<?php
namespace App\Modules\Lease\Http\Requests\Leases;

use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;
use Illuminate\Foundation\Http\FormRequest;

class LeaseDatatableRequest extends FormRequest
{
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
            'property_id'         => 'sometimes|nullable|integer|exists:' . Property::getTableName() . ',id',
            'unit_id'             => 'sometimes|nullable|integer|exists:' . Unit::getTableName() . ',id',
            'payable'             => 'sometimes|boolean',
        ];
    }
}
