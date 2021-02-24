<?php
namespace App\Modules\Workorder\Http\Requests\Quotes;

use App\Modules\Workorder\Models\Quote;
use Illuminate\Foundation\Http\FormRequest;

class QuoteStoreRequest extends FormRequest
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
            'property_id'          => 'integer',
            'unit_id'              => 'integer',
            'supplier_id'          => 'integer|required',
            'expenditure_type_id'  => 'integer|required',
            'value'                => 'sometimes|numeric',
            'work_description'     => 'string|required',
            'critical_information' => 'string|nullable',
            'contact_details'      => 'string|nullable',
            'booked_at'            => 'date|sometimes',
            'due_at'               => 'date|sometimes',
            'deleted_at'           => 'date|sometimes',
            'deleted_note'         => 'string|max:255',
        ];
    }
}
