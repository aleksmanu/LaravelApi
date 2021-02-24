<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/19/18
 * Time: 11:05 AM
 */

namespace App\Modules\Property\Http\Requests\PropertyUses;

use App\Modules\Property\Models\PropertyUse;
use Illuminate\Foundation\Http\FormRequest;

class PropertyUseStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:' . PropertyUse::getTableName(),
        ];
    }
}
