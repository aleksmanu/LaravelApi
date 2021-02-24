<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/24/18
 * Time: 12:27 PM
 */

namespace App\Modules\Lease\Http\Requests\Leases;

use App\Modules\Lease\Models\BreakPartyOption;
use App\Modules\Lease\Models\HeadLease;
use App\Modules\Lease\Models\LeaseType;
use App\Modules\Lease\Models\RentFrequency;
use App\Modules\Lease\Models\ReviewType;
use App\Modules\Lease\Models\VatRate;
use App\Modules\Property\Models\Unit;
use Illuminate\Foundation\Http\FormRequest;

class LeaseStoreRequest extends FormRequest
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
            'annual_rent_vat_rate'           => 'nullable|regex:/^\d*(\.\d{1,2})?$/',
            'annual_service_charge_vat_rate' => 'nullable|regex:/^\d*(\.\d{1,2})?$/',
            'lease_type_id'                  => 'nullable|integer|exists:' . LeaseType::getTableName() . ',id',
            'break_party_option_id'          => 'nullable|integer|exists:' . BreakPartyOption::getTableName() . ',id',
            'rent_frequency_id'              => 'nullable|integer|exists:' . RentFrequency::getTableName() . ',id',
            'review_type_id'                 => 'nullable|integer|exists:' . ReviewType::getTableName() . ',id',
            'unit_id'                        => 'required|integer|exists:' . Unit::getTableName() . ',id',
            'break_notice_days'              => 'nullable|integer',
            'yardi_tenant_ref'               => 'required|string|max:255',
            'annual_rent'                    => 'nullable|numeric',
            'annual_service_charge'          => 'nullable|numeric',
            'live'                           => 'nullable|boolean',
            'next_break_at'                  => 'nullable|date',
            'next_review_at'                 => 'nullable|date',
            'expiry_at'                      => 'nullable|date',
            'commencement_at'                => 'nullable|date',
            'approved_at'                    => 'nullable|date',
            'approved_initials'              => 'nullable|string|max:5',
            'approved'                       => 'nullable|boolean',
            'held_at'                        => 'nullable|date',
            'held_initials'                  => 'nullable|string|max:5'
        ];
    }
}
