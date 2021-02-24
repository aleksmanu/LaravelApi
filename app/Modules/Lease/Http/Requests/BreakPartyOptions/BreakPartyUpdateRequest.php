<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/24/18
 * Time: 12:06 PM
 */

namespace App\Modules\Lease\Http\Requests\BreakPartyOptions;

use App\Modules\Lease\Models\BreakPartyOption;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BreakPartyUpdateRequest extends FormRequest
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
                Rule::unique(BreakPartyOption::getTableName())->ignore($this->route('breakPartyOption'))
            ],
        ];
    }
}
