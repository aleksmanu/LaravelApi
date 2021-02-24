<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/19/18
 * Time: 12:32 PM
 */

namespace App\Modules\Lease\Http\Requests\TenantStatuses;

use App\Modules\Lease\Models\TenantStatus;
use Illuminate\Foundation\Http\FormRequest;

class TenantStatusStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // More granular rules can go here, return false to halt
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
            'name' => 'required|string|max:128|unique:' . TenantStatus::getTableName(),
        ];
    }
}
