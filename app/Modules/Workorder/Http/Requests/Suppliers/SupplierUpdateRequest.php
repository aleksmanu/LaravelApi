<?php
namespace App\Modules\Workorder\Http\Requests\Suppliers;

use App\Modules\Workorder\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierUpdateRequest extends FormRequest
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
            'name'       => 'sometimes|string|max:128',
            'phone'      => 'sometimes|string|max:64',
            'email'      => 'sometimes|string|max:128',
            'deleted_at' => 'sometimes|date',
        ];
    }
}
