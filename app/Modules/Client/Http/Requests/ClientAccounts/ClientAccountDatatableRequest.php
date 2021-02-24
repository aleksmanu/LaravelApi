<?php

namespace App\Modules\Client\Http\Requests\ClientAccounts;

use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\ClientAccountStatus;
use App\Modules\Client\Models\OrganisationType;
use App\Modules\Property\Models\PropertyManager;
use Illuminate\Foundation\Http\FormRequest;

class ClientAccountDatatableRequest extends FormRequest
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
            'sort_column'              => 'sometimes|required',
            'sort_order'               => 'sometimes|required',
            'offset'                   => 'sometimes|numeric|required',
            'limit'                    => 'sometimes|numeric',
            'property_manager_id'      => 'sometimes|nullable|exists:' . PropertyManager::getTableName() . ',id',
            'client_account_status_id' => 'sometimes|nullable|exists:' . ClientAccountStatus::getTableName() .',id',
            'organisation_type_id'     => 'sometimes|nullable|exists:' . OrganisationType::getTableName() . ',id',
            'client_account_name_partial' => 'sometimes|string|max:255',
        ];
    }
}
