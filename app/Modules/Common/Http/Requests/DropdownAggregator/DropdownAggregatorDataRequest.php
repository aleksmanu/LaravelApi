<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 10/3/18
 * Time: 9:58 AM
 */

namespace App\Modules\Common\Http\Requests\DropdownAggregator;

use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\ClientAccountStatus;
use App\Modules\Client\Models\OrganisationType;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Models\Address;
use App\Modules\Common\Models\Country;
use App\Modules\Common\Models\County;
use App\Modules\Property\Models\LocationType;
use App\Modules\Property\Models\MeasurementUnit;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\PropertyCategory;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Property\Models\PropertyStatus;
use App\Modules\Property\Models\PropertyTenure;
use App\Modules\Property\Models\PropertyUse;
use App\Modules\Property\Models\StopPosting;
use Illuminate\Foundation\Http\FormRequest;

class DropdownAggregatorDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // More granular rules can go here, return false to halt
        $request = request();

        if ($request->has('account_id') && \Bouncer::cannot('index', ClientAccountStatus::class)) {
            return false;
        }
        if ($request->has('client_account_status_id') && \Bouncer::cannot('index', ClientAccountStatus::class)) {
            return false;
        }
        if ($request->has('client_account_id') && \Bouncer::cannot('index', ClientAccount::class)) {
            return false;
        }
        if ($request->has('property_manager_id') && \Bouncer::cannot('index', PropertyManager::class)) {
            return false;
        }
        if ($request->has('portfolio_id') && \Bouncer::cannot('index', Portfolio::class)) {
            return false;
        }
        if ($request->has('property_id') && \Bouncer::cannot('index', Property::class)) {
            return false;
        }
        if ($request->has('address_id') && \Bouncer::cannot('index', Address::class)) {
            return false;
        }
        if ($request->has('organisation_type_id') && \Bouncer::cannot('index', OrganisationType::class)) {
            return false;
        }
        if ($request->has('county_id') && \Bouncer::cannot('index', County::class)) {
            return false;
        }
        if ($request->has('country_id') && \Bouncer::cannot('index', Country::class)) {
            return false;
        }
        if ($request->has('property_status_id') && \Bouncer::cannot('index', PropertyStatus::class)) {
            return false;
        }
        if ($request->has('property_use_id') && \Bouncer::cannot('index', PropertyUse::class)) {
            return false;
        }
        if ($request->has('property_tenure_id') && \Bouncer::cannot('index', PropertyTenure::class)) {
            return false;
        }
        if ($request->has('location_type_id') && \Bouncer::cannot('index', LocationType::class)) {
            return false;
        }
        if ($request->has('property_category_id') && \Bouncer::cannot('index', PropertyCategory::class)) {
            return false;
        }
        if ($request->has('stop_posting_id') && \Bouncer::cannot('index', StopPosting::class)) {
            return false;
        }
        if ($request->has('measurement_unit_id') && \Bouncer::cannot('index', MeasurementUnit::class)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
