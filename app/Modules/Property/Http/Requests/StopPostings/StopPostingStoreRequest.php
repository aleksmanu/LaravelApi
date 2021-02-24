<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 10/25/18
 * Time: 9:27 AM
 */

namespace App\Modules\Property\Http\Requests\StopPostings;

use App\Modules\Property\Models\StopPosting;
use Illuminate\Foundation\Http\FormRequest;

class StopPostingStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:' . StopPosting::getTableName(),
        ];
    }
}
