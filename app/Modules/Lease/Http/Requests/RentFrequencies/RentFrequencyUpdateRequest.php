<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/21/18
 * Time: 11:07 AM
 */

namespace App\Modules\Lease\Http\Requests\RentFrequencies;

use App\Modules\Lease\Models\RentFrequency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RentFrequencyUpdateRequest extends FormRequest
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
                Rule::unique(RentFrequency::getTableName())->ignore($this->route('rentFrequency'))
            ],
        ];
    }
}
