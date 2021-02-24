<?php

namespace App\Modules\Attachments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentPatchRequest extends FormRequest
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
            'document_type_id' => 'sometimes',
            'parties'          => 'sometimes|string',
            'comments'         => 'sometimes|string',
            'reference'        => 'sometimes|string',
            'date'             => 'nullable|date',
            'archived_at'      => 'sometimes'
        ];
    }
}
