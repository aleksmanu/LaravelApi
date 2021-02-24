<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/21/18
 * Time: 11:43 AM
 */

namespace App\Modules\Property\Http\Requests\MeasurementUnits;

use App\Modules\Property\Models\MeasurementUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MeasurementUnitUpdateRequest extends FormRequest
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
                'max:64',
                Rule::unique(MeasurementUnit::getTableName())->ignore($this->route('measurementUnit'))
            ],
        ];
    }
}
