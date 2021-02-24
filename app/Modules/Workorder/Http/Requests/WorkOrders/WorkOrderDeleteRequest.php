<?php
namespace App\Modules\Workorder\Http\Requests\WorkOrders;

use Illuminate\Foundation\Http\FormRequest;

class WorkOrderDeleteRequest extends FormRequest
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
            'locked_note' => 'required|string|max:255',
        ];
    }
}
