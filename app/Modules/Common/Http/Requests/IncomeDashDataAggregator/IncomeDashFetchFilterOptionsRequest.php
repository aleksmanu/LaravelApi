<?php

namespace App\Modules\Common\Http\Requests\IncomeDashDataAggregator;

use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Lease\Models\Lease;
use App\Modules\Property\Models\LocationType;
use App\Modules\Property\Models\PropertyCategory;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Property\Models\PropertyStatus;
use App\Modules\Property\Models\PropertyTenure;
use App\Modules\Property\Models\PropertyUse;
use Illuminate\Foundation\Http\FormRequest;

class IncomeDashFetchFilterOptionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return (\Bouncer::can('index', ClientAccount::class)
            && \Bouncer::can('index', PropertyManager::class)
            && \Bouncer::can('index', Portfolio::class)
            && \Bouncer::can('index', PropertyStatus::class)
            && \Bouncer::can('index', PropertyUse::class)
            && \Bouncer::can('index', PropertyTenure::class)
            && \Bouncer::can('index', LocationType::class)
            && \Bouncer::can('index', PropertyCategory::class)
            && \Bouncer::can('index', Lease::class));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'client_account_id'  => 'sometimes|integer|exists:' . ClientAccount::getTableName() . ',id',
            'portfolio_id' => 'sometimes|integer|exists:' . Portfolio::getTableName() . ',id',
            'property_manager_id' => 'sometimes|integer|exists:' . PropertyManager::getTableName() . ',id',
            'property_use_id' => 'sometimes|integer|exists:' . PropertyUse::getTableName() . ',id',
            'property_tenure_id' => 'sometimes|integer|exists:' . PropertyTenure::getTableName() . ',id',
            'property_category_id' => 'sometimes|integer|exists:' . PropertyCategory::getTableName() . ',id',
            'min_year' => 'sometimes|integer',
            'max_year' => 'sometimes|integer',
            'leaseType' => 'sometimes',
            'property_live' => 'sometimes|boolean',
            'addr_town' => 'sometimes',
            'conservation_area' => 'sometimes|boolean',
            'listed_building' => 'sometimes|boolean',

            'unit_void' => 'sometimes|boolean',
            'unit_fake' => 'sometimes|boolean',

            'lease_lt_name' => 'sometimes',
            'lease_outside_lt_act' => 'sometimes|boolean',
            'lease_holding_over' => 'sometimes|boolean',
            'lease_tenure' => 'sometimes',
        ];
    }
}
