<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/19/18
 * Time: 11:30 AM
 */

namespace App\Modules\Property\Http\Requests\PropertyCategories;

use App\Modules\Property\Models\PropertyCategory;
use Illuminate\Foundation\Http\FormRequest;

class PropertyCategoryStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:' . PropertyCategory::getTableName(),
        ];
    }
}
