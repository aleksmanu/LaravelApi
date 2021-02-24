<?php
namespace App\Modules\Workorder\Http\Requests\ExpenditureTypes;

use App\Modules\Workorder\Models\ExpenditureType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenditureTypeUpdateRequest extends FormRequest
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
            'name'       => 'sometimes|string|max:64',
            'code'       => 'sometimes|string|max:16|unique' . ExpenditureType::getTableName(),
            'deleted_at' => 'sometimes|date',
        ];
    }
}
