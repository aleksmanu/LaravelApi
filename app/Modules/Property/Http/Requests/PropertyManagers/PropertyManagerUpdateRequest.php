<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/18/18
 * Time: 11:49 AM
 */

namespace App\Modules\Property\Http\Requests\PropertyManagers;

use App\Modules\Property\Models\PropertyManager;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropertyManagerUpdateRequest extends FormRequest
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
            'user_id' => [
                'sometimes',
                'exists:users,id',
                Rule::unique(PropertyManager::getTableName())->ignore($this->route('propertyManager'))
            ],
            'code'    => 'required|string|max:10'
        ];
    }
}
