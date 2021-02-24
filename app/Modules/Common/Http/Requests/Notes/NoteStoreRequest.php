<?php

namespace App\Modules\Common\Http\Requests\Notes;

use Illuminate\Foundation\Http\FormRequest;

class NoteStoreRequest extends FormRequest
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
            'note'        => 'required|string',
            'is_internal' => 'sometimes|boolean',
            'divider'     => 'sometimes|string|nullable'
        ];
    }
}
