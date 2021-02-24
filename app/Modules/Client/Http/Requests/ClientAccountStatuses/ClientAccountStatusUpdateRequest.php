<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/19/18
 * Time: 8:51 AM
 */

namespace App\Modules\Client\Http\Requests\ClientAccountStatuses;

use App\Modules\Client\Models\ClientAccountStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientAccountStatusUpdateRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(ClientAccountStatus::getTableName())->ignore($this->route('clientAccountStatus'))
            ],
        ];
    }
}
