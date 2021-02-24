<?php

namespace App\Modules\Attachments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentTypeIndexRequest extends FormRequest
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
            'document_level_id' => 'sometimes|integer',
            'document_level_name' => 'sometimes|string'
        ];
    }
}
