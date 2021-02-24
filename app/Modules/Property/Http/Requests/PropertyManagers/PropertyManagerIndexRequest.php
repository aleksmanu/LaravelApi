<?php

namespace App\Modules\Property\Http\Requests\PropertyManagers;

use App\Modules\Property\Models\PropertyManager;
use Illuminate\Foundation\Http\FormRequest;

class PropertyManagerIndexRequest extends FormRequest
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
            //
        ];
    }
}
