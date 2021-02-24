<?php

namespace App\Modules\Client\Http\Requests\ClientAccounts;

use App\Modules\Account\Models\Account;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\ClientAccountStatus;
use App\Modules\Client\Models\OrganisationType;
use App\Modules\Common\Models\Address;
use App\Modules\Property\Models\PropertyManager;
use Illuminate\Foundation\Http\FormRequest;

class ClientAccountStoreRequest extends FormRequest
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
            'organisation_type_id'     => 'required|integer|exists:' . OrganisationType::getTableName() . ',id',
            'property_manager_id'      => 'required|integer|exists:' . PropertyManager::getTableName() . ',id',
            'client_account_status_id' => 'required|integer|exists:' . ClientAccountStatus::getTableName() . ',id',
            'name'                     => 'required|string|max:255|unique:' . ClientAccount::getTableName(),
            'yardi_client_ref'         => 'required|string|max:20',
            'yardi_alt_ref'            => 'nullable|string|max:20',
            'addr_unit'                => 'sometimes|nullable',
            'addr_number'              => 'sometimes|nullable',
            'addr_building'            => 'sometimes|nullable',
            'addr_street'              => 'sometimes|nullable',
            'addr_estate'              => 'sometimes|nullable',
            'addr_suburb'              => 'sometimes|nullable',
            'addr_town'                => 'sometimes|nullable',
            'addr_postcode'            => 'sometimes|nullable',
            'county_id'                => 'nullable',
            'country_id'               => 'nullable',
            'edit'                     => 'nullable|boolean'
        ];
    }
}
