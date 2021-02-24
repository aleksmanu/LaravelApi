<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/19/18
 * Time: 11:40 AM
 */

namespace App\Modules\Property\Http\Requests\LocationTypes;

use App\Modules\Property\Models\LocationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocationTypeUpdateRequest extends FormRequest
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
                Rule::unique(LocationType::getTableName())->ignore($this->route('locationType'))
            ],
        ];
    }
}
