<?php

namespace App\Modules\Acquisition\Http\Requests;

use App\Modules\Account\Models\Account;
use Illuminate\Foundation\Http\FormRequest;

class AcquisitionUpdateRequest extends FormRequest
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
            'pop_areas' => 'sometimes',
            'users' => 'sometimes',
            'sites' => 'sometimes',
        ];
    }
}
