<?php
namespace App\Modules\Attachments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadWithCatRequest extends FormRequest
{
    /**
     * @return true
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'file'             => 'required|file|max:64000',
            'reference'        => 'nullable|string',
            'document_type_id' => 'required|integer',
            'date'             => 'sometimes',
            'parties'          => 'sometimes|string',
            'comments'         => 'sometimes|string'
        ];
    }
}
