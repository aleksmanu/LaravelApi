<?php

namespace App\Modules\Edits\Http\Requests\EditRequests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEditsRequest extends FormRequest
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
            'approved' => 'required|boolean',
            'edits'    => 'required|array',
            'note'     => 'sometimes|nullable'
        ];
    }
}
