<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/20/18
 * Time: 3:15 PM
 */

namespace App\Modules\Property\Http\Requests\Properties;

use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Models\Address;
use App\Modules\Property\Models\LocationType;
use App\Modules\Property\Models\PropertyCategory;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Property\Models\PropertyStatus;
use App\Modules\Property\Models\PropertyUse;
use App\Modules\Property\Models\PropertyTenure;
use App\Modules\Property\Models\StopPosting;
use Illuminate\Foundation\Http\FormRequest;

class PropertyUpdateRequest extends FormRequest
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
            'portfolio_id'              => 'required|integer|exists:' . Portfolio::getTableName() . ',id',
            'property_manager_id'       => 'required|integer|exists:' . PropertyManager::getTableName() . ',id',
            //'address_id'                => 'required|integer|exists:' . Address::getTableName() . ',id',
            'property_status_id'        => 'required|integer|exists:' . PropertyStatus::getTableName() . ',id',
            'property_use_id'           => 'nullable|integer|exists:' . PropertyUse::getTableName() . ',id',
            'property_tenure_id'        => 'required|integer|exists:' . PropertyTenure::getTableName() . ',id',
            'location_type_id'          => 'required|integer|exists:' . LocationType::getTableName() . ',id',
            'property_category_id'      => 'nullable|integer|exists:' . PropertyCategory::getTableName() . ',id',
            'stop_posting_id'           => 'required|integer|exists:' . StopPosting::getTableName() . ',id',
            'name'                      => 'required|string|max:255',
            'yardi_property_ref'        => 'required|string|max:20',
            'yardi_alt_ref'             => 'nullable|string|max:20',
            'total_lettable_area'       => 'nullable|regex:/^\d*(\.\d{1,2})?$/',
            'void_total_lettable_area'  => 'nullable|regex:/^\d*(\.\d{1,2})?$/',
            'total_site_area'           => 'nullable|regex:/^\d*(\.\d{1,2})?$/',
            'total_gross_internal_area' => 'nullable|regex:/^\d*(\.\d{1,2})?$/',
            'total_rateable_value'      => 'nullable|regex:/^\d*(\.\d{1,2})?$/',
            'void_total_rateable_value' => 'nullable|regex:/^\d*(\.\d{1,2})?$/',
            'listed_building'           => 'nullable|boolean',
            'live'                      => 'nullable|boolean',
            'conservation_area'         => 'nullable|boolean',
            'air_conditioned'           => 'nullable|boolean',
            'vat_registered'            => 'nullable|boolean',
            'approved'                  => 'nullable|boolean',
            'approved_at'               => 'nullable|date',
            'approved_initials'         => 'nullable|string|max:5',
            'held_at'                   => 'nullable|date',
            'held_initials'             => 'nullable|string|max:5',
            'edit'                      => 'nullable|boolean',

            'addr_unit'     => 'sometimes|nullable',
            'addr_number'   => 'sometimes|nullable',
            'addr_building' => 'sometimes|nullable',
            'addr_street'   => 'sometimes|nullable',
            'addr_estate'   => 'sometimes|nullable',
            'addr_suburb'   => 'sometimes|nullable',
            'addr_town'     => 'sometimes|nullable',
            'addr_postcode' => 'sometimes|nullable',
            'addr_lat' => 'sometimes|nullable',
            'addr_long' => 'sometimes|nullable',
            'county_id'     => 'nullable',
            'country_id'    => 'nullable'
        ];
    }
}
