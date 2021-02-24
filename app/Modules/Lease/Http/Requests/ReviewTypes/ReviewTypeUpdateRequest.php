<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/19/18
 * Time: 12:14 PM
 */

namespace App\Modules\Lease\Http\Requests\ReviewTypes;

use App\Modules\Lease\Models\ReviewType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewTypeUpdateRequest extends FormRequest
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
                Rule::unique(ReviewType::getTableName())->ignore($this->route('reviewType'))
            ],
        ];
    }
}
