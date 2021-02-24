<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/21/18
 * Time: 11:22 AM
 */

namespace App\Modules\Lease\Http\Requests\LeaseTypes;

use App\Modules\Lease\Models\LeaseType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeaseTypeUpdateRequest extends FormRequest
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
                'max:128',
                Rule::unique(LeaseType::getTableName())->ignore($this->route('leaseType'))
            ],
        ];
    }
}
