<?php

namespace App\Modules\Workorder\Http\Requests\Quotes;

use Illuminate\Foundation\Http\FormRequest;

class QuoteAcceptRequest extends FormRequest
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
            'value' => 'required|numeric',
            'locked_note' => 'sometimes|string|max:255'
        ];
    }
}
