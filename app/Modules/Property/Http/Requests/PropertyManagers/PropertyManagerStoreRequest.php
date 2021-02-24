<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/18/18
 * Time: 11:16 AM
 */

namespace App\Modules\Property\Http\Requests\PropertyManagers;

use App\Modules\Property\Models\PropertyManager;
use Illuminate\Foundation\Http\FormRequest;

class PropertyManagerStoreRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id|unique:' . PropertyManager::getTableName(),
            'code'    => 'required|string|max:10'
        ];
    }
}
