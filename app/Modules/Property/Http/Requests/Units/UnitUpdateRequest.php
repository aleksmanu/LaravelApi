<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/26/18
 * Time: 9:48 AM
 */

namespace App\Modules\Property\Http\Requests\Units;

use App\Modules\Auth\Models\User;
use App\Modules\Property\Models\MeasurementUnit;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\PropertyManager;
use Illuminate\Foundation\Http\FormRequest;

class UnitUpdateRequest extends FormRequest
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
            'property_id'             => 'required|integer|exists:' . Property::getTableName() . ',id',
            'property_manager_id'     => 'required|integer|exists:' . PropertyManager::getTableName() . ',id',
            'measurement_unit_id'     => 'nullable|integer|exists:' . MeasurementUnit::getTableName() . ',id',
            'demise'                  => 'required|string|max:255',
            'unit'                    => 'nullable|string|max:10',
            'name'                    => 'nullable|string|max:255',
            'yardi_unit_ref'          => 'required|string|max:255',
            'yardi_import_ref'        => 'nullable|string|max:255',
            'yardi_property_unit_ref' => 'nullable|string|max:255',
            'measurement_value'       => 'nullable|numeric',
            'approved_at'             => 'nullable|date',
            'approved'                => 'nullable|boolean',
            'approved_initials'       => 'nullable|string|max:255',
            'held_at'                 => 'nullable|date',
            'held_initials'           => 'nullable|string|max:255',
            'edit'                    => 'nullable|boolean'
        ];
    }
}
