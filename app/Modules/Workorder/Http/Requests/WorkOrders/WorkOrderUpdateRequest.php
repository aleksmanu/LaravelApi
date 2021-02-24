<?php
namespace App\Modules\Workorder\Http\Requests\WorkOrders;

use App\Modules\Workorder\Models\Quote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkOrderUpdateRequest extends FormRequest
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
            'quote_id'     => 'integer|exists:' . Quote::getTableName() . ',id',
            'value'        => 'integer|nullable',
            'paid_at'      => 'date|nullable',
            'completed_at' => 'date|nullable',
            'deleted_at_at' => 'date|nullable',
        ];
    }
}
