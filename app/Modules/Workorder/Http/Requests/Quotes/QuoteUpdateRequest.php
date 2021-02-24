<?php
namespace App\Modules\Workorder\Http\Requests\Quotes;

use App\Modules\Workorder\Models\Quote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuoteUpdateRequest extends FormRequest
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
            'supplier_id'          => 'integer',
            'expenditure_type_id'  => 'integer',
            'value'                => 'integer',
            'work_description'     => 'string',
            'critical_information' => 'string|nullable',
            'contact_details'      => 'string|nullable',
            'booked_at'            => 'date|nullable',
            'due_at'               => 'date|nullable',
            'deleted_at'           => 'date',
            'deleted_note'         => 'string|max:255',
        ];
    }
}
