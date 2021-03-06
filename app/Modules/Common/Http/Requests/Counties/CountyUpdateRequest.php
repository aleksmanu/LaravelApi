<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/24/18
 * Time: 9:43 AM
 */

namespace App\Modules\Common\Http\Requests\Counties;

use App\Modules\Common\Models\Region;
use Illuminate\Foundation\Http\FormRequest;

class CountyUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
        ];
    }
}
