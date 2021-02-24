<?php
namespace App\Modules\Workorder\Http\Requests\WorkOrders;

use App\Modules\Workorder\Models\WorkOrder;
use App\Modules\Workorder\Models\Quote;
use Illuminate\Foundation\Http\FormRequest;

class WorkOrderStoreRequest extends FormRequest
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
            'quote_id' => 'integer|required|exists:' . Quote::getTableName() . ',id',
            'value'    => 'integer|required',
        ];
    }
}
