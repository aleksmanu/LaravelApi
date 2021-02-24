<?php

namespace App\Modules\Attachments\Http\Requests;

use App\Modules\Attachments\Models\DocumentCategory;
use Illuminate\Foundation\Http\FormRequest;

class DocumentTypeStoreRequest extends FormRequest
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
            'name' => 'required|string',
            'levels' => 'required|array',
            'levels.*' => 'integer',
            'document_category_id' => 'required|integer|exists:' . DocumentCategory::getTableName() . ',id'
        ];
    }
}
