<?php

namespace App\Modules\Edits\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditBatchDatatableRequest extends FormRequest
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
            'edit_batch_type_id' => 'nullable|exists:edit_batch_types,id',
            'offset'             => 'integer',
            'limit'              => 'integer',
            'sort_col'           => 'required',
            'sort_dir'           => 'required'
        ];
    }
}
