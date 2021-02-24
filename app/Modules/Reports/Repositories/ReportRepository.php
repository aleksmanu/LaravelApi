<?php

namespace App\Modules\Reports\Repositories;

use App\Modules\Lease\Models\Lease;
use App\Modules\Core\Classes\Repository;
use App\Modules\Lease\Models\LeaseBreak;
use App\Modules\Lease\Models\LeaseCharge;
use App\Modules\Lease\Models\LeaseChargeType;
use App\Modules\Reports\Models\Report;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;
use App\Modules\Lease\Models\ChargeHistory;
use Illuminate\Support\Facades\DB;

class ReportRepository extends Repository
{
    // private $leaseEntityType = "'App\\\\Modules\\\\Lease\\\\Models\\\\Lease'";

    public $rentFrequency;
    public $leaseBreak;
    public $leasePayables;
    public $rentProfile;

    /**
     * ReportRepository constructor.
     * @param Report $model
     */
    public function __construct(Report $model)
    {
        $this->model = $model;
        $this->rentFrequency =  LeaseCharge::select([
            'frequency',
            'entity_id'
        ])
            ->orderBy('lease_charges.entity_id', 'ASC')
            ->groupBy('lease_charges.entity_id');

        $this->leaseBreak = LeaseBreak::select([
            'id',
            'break_party_option_id',
            'entity_id',
            'type',
            DB::raw('min(date) as `date`'),
            'min_notice',
            'penalty',
            'penalty_incentive',
            'notes',
            'created_at',
            'updated_at',
        ])->whereNotIn('lease_breaks.id', [29,81,84,89,95])
            ->orderBy('lease_breaks.entity_id', 'ASC')
            ->groupBy('lease_breaks.entity_id');

        $leasesPayables = Lease::join('units', 'leases.unit_id', '=', 'units.id')
            ->join('properties', 'units.property_id', '=', 'properties.id')
            ->join('portfolios', 'properties.portfolio_id', '=', 'portfolios.id')
            ->join('client_accounts', 'portfolios.client_account_id', '=', 'client_accounts.id')
            ->join('accounts', 'client_accounts.account_id', '=', 'accounts.id')
            ->join('addresses', 'properties.address_id', '=', 'addresses.id')
            ->leftJoin('property_uses', 'properties.property_use_id', '=', 'property_uses.id')
            ->leftJoin('property_tenures', 'properties.property_tenure_id', '=', 'property_tenures.id')
            ->leftJoin('counties', 'addresses.county_id', '=', 'counties.id')
            ->leftJoin('countries', 'addresses.country_id', '=', 'countries.id')
            ->join('property_managers', 'units.property_manager_id', '=', 'property_managers.id')
            ->join('users as property_manager_user', 'property_managers.user_id', '=', 'property_manager_user.id')
            ->leftJoin('lease_types', 'leases.type_id', '=', 'lease_types.id')
            ->where('properties.live', true)
            ->where('leases.live', true)
            ->where('leases.payable', true);

        $this->leasePayables = $leasesPayables;
    }

    public function all()
    {
        return Report::all();
    }

    public function find($id)
    {
        return Report::find($id);
    }

    public function getData($id, bool $csv, $constrainIds = null)
    {
        $report = $this->find($id);
        $select = [];
        $headers = [];
        $toReturn = [];
        foreach ($report->reportColumns as $col) {
            if ($report->source == 'lease_payable_all' || $report->source == 'lease_payable_rent' || $report->source == 'lease_payable_break' || $report->source == 'lease_payable_expiry') {
                switch ($col->attribute) {
                    case 'leases.passing_rent':
                        $select[] = "rent_qry.rent_sum as `$col->name`";
                        break;
                    case 'leases.service_charge':
                        $select[] = "IFNULL(service_charge_qry.service_charge_sum,0) as `$col->name`";
                        break;
                    case 'leases.insurance':
                        $select[] = "IFNULL(insurance_charge_qry.insurance_charge_sum,0) as `$col->name`";
                        break;
                    case 'leases.rates_liability':
                        $select[] = "IFNULL(rate_charge_qry.rate_charge_sum,0) as `$col->name`";
                        break;
                    default:
                        $select[] = $col->attribute . " as `$col->name`";
                        break;
                }
            } else {
                $select[] = $col->attribute . " as `$col->name`";
            }

            $headers[] = $col->name;
        }

        if ($csv) {
            $toReturn[] = $headers;
        }

        $select = implode(',', $select);
        switch ($report->source) {
            case 'property':
                $data = $this->getProperties();

                if ($constrainIds) {
                    $data = $data->whereIn('properties.id', $constrainIds);
                }
                break;

            case 'unit':
                $data = $this->getUnits();

                if ($constrainIds) {
                    $data = $data->whereIn('units.id', $constrainIds);
                }
                break;
            case 'lease_payable_all':
                $data = $this->getPayables();

                if ($constrainIds) {
                    $data = $data->whereIn('leases.id', $constrainIds);
                }

                break;

            case 'lease_payable_rent':
                $data = $this->getReviews();

                if ($constrainIds) {
                    $data = $data->whereIn('leases.id', $constrainIds);
                }
                break;

            case 'lease_payable_break':
                $data = $this->getBreaks();

                if ($constrainIds) {
                    $data = $data->whereIn('leases.id', $constrainIds);
                }

                break;

            case 'lease_payable_expiry':
                $data = $this->getExpiry();

                if ($constrainIds) {
                    $data = $data->whereIn('leases.id', $constrainIds);
                }
                break;

            case 'lease_rent_profile':
                $data = $this->getRentProfile();

                if ($constrainIds) {
                    $data = $data->whereIn('charge_history.id', $constrainIds);
                }

                break;
            default:
                return 'No matching data source';
                break;
        }
        $data = $data->select(DB::raw($select))->get();

        foreach ($data->toArray() as $datum) {
            $cleaned = [];
            foreach ($datum as $key => $value) {
                if (in_array($key, $headers)) {
                    $value = preg_replace('/\n/', ' ', $value);
                    if ($csv) {
                        $cleaned[] = '"' . $value . '"';
                    } else {
                        $cleaned[$key] = $value;
                    }
                }
            }
            $toReturn[] = $cleaned;
        }

        return $toReturn;
    }

    private function getProperties()
    {
        return Property::join('portfolios', 'properties.portfolio_id', '=', 'portfolios.id')
            ->join('client_accounts', 'portfolios.client_account_id', '=', 'client_accounts.id')
            ->join('accounts', 'client_accounts.account_id', '=', 'accounts.id')
            ->join('addresses', 'properties.address_id', '=', 'addresses.id')
            ->leftJoin('property_uses', 'properties.property_use_id', '=', 'property_uses.id')
            ->leftJoin('property_tenures', 'properties.property_tenure_id', '=', 'property_tenures.id')
            ->leftJoin('counties', 'addresses.county_id', '=', 'counties.id')
            ->leftJoin('countries', 'addresses.country_id', '=', 'countries.id')
            ->join('property_managers', 'properties.property_manager_id', '=', 'property_managers.id')
            ->join('users as property_manager_user', 'property_managers.user_id', '=', 'property_manager_user.id')
            ->where('properties.live', true);
    }

    private function getUnits()
    {
        return Unit::join('properties', 'units.property_id', '=', 'properties.id')
            ->join('portfolios', 'properties.portfolio_id', '=', 'portfolios.id')
            ->join('client_accounts', 'portfolios.client_account_id', '=', 'client_accounts.id')
            ->join('accounts', 'client_accounts.account_id', '=', 'accounts.id')
            ->join('addresses', 'properties.address_id', '=', 'addresses.id')
            ->leftJoin('property_uses', 'properties.property_use_id', '=', 'property_uses.id')
            ->leftJoin('property_tenures', 'properties.property_tenure_id', '=', 'property_tenures.id')
            ->leftJoin('counties', 'addresses.county_id', '=', 'counties.id')
            ->leftJoin('countries', 'addresses.country_id', '=', 'countries.id')
            ->join('property_managers', 'units.property_manager_id', '=', 'property_managers.id')
            ->join('users as property_manager_user', 'property_managers.user_id', '=', 'property_manager_user.id')
            ->where('properties.live', true)
            ->where('units.is_virtual', 0);
    }

    private function getPayables()
    {
        $q = $this->leasePayables
            ->leftJoin('agents as landlord', 'leases.landlord_id', '=', 'landlord.id')
            ->leftJoin('agents as managing_agent', 'leases.managing_agent_id', '=', 'managing_agent.id')
            ->leftJoinSub($this->leaseBreak, 'lease_breaks', function ($join) {
                $join->on('lease_breaks.entity_id', '=', 'leases.id');
            })
            ->leftJoin('break_party_options', 'lease_breaks.break_party_option_id', '=', 'break_party_options.id')
            ->leftJoinSub($this->rentFrequency, 'lease_charges', function ($join) {
                $join->on('lease_charges.entity_id', '=', 'leases.id');
            });

        return $this->makeChargesSumQry($q)
            ->groupBy('leases.id');
    }

    private function getReviews()
    {
        $q = $this->leasePayables
            ->leftJoin('agents as landlord', 'leases.landlord_id', '=', 'landlord.id')
            ->leftJoin('agents as managing_agent', 'leases.managing_agent_id', '=', 'managing_agent.id')
            ->leftJoinSub($this->leaseBreak, 'lease_breaks', function ($join) {
                $join->on('lease_breaks.entity_id', '=', 'leases.id');
            })
            ->leftJoin('break_party_options', 'lease_breaks.break_party_option_id', '=', 'break_party_options.id')
            ->leftJoinSub($this->rentFrequency, 'lease_charges', function ($join) {
                $join->on('lease_charges.entity_id', '=', 'leases.id');
            });

        return $this->makeChargesSumQry($q)
            ->whereNotNull('leases.next_review')
            ->groupBy('leases.id');
    }

    private function getBreaks()
    {
        $q = $this->leasePayables
            ->leftJoin('agents as landlord', 'leases.landlord_id', '=', 'landlord.id')
            ->leftJoin('agents as managing_agent', 'leases.managing_agent_id', '=', 'managing_agent.id')
            ->leftJoinSub($this->leaseBreak, 'lease_breaks', function ($join) {
                $join->on('lease_breaks.entity_id', '=', 'leases.id');
            })
            ->join('break_party_options', 'lease_breaks.break_party_option_id', '=', 'break_party_options.id');

        return $this->makeChargesSumQry($q)
            ->groupBy('leases.id');
    }

    private function getExpiry()
    {
        $q = $this->leasePayables
            ->leftJoin('agents as landlord', 'leases.landlord_id', '=', 'landlord.id')
            ->leftJoin('agents as managing_agent', 'leases.managing_agent_id', '=', 'managing_agent.id')
            ->leftJoinSub(
                $this->leaseBreak,
                'next_break',
                function ($join) {
                    $join->on('next_break.entity_id', '=', 'leases.id');
                }
            )
            ->leftJoin('break_party_options', 'next_break.break_party_option_id', '=', 'break_party_options.id');

        return $this->makeChargesSumQry($q)
            ->groupBy('leases.id');
    }

    private function getRentProfile()
    {
        return ChargeHistory::join('leases', 'charge_history.entity_id', '=', 'leases.id')
            ->join('lease_types', 'leases.type_id', '=', 'lease_types.id')
            ->leftJoin('agents as landlord', 'leases.landlord_id', '=', 'landlord.id')
            ->join('units', 'leases.unit_id', '=', 'units.id')
            ->join('properties', 'units.property_id', '=', 'properties.id')
            ->join('portfolios', 'properties.portfolio_id', '=', 'portfolios.id')
            ->join('client_accounts', 'portfolios.client_account_id', '=', 'client_accounts.id')
            ->join('accounts', 'client_accounts.account_id', '=', 'accounts.id')
            ->join('addresses', 'properties.address_id', '=', 'addresses.id')
            ->where('charge_history.type_id', 81)
            ->groupBy('charge_history.id')
            ->orderBy('leases.cluttons_lease_ref', 'ASC')
            ->orderBy('charge_history.changed_on', 'DESC');
    }

    /**
     * Add lease charges sum query to get correct report data
     *
     * @param $q
     * @return mixed
     */
    private function makeChargesSumQry(&$q) {
        return $q
            ->leftJoinSub(
                DB::table('lease_charges')
                    ->select('entity_id', DB::raw('SUM(annual) as rent_sum'))
                    ->whereIn('lease_charge_type', LeaseChargeType::RENT_TYPES)
                    ->where('entity_type', Lease::class)
                    ->whereNull('deleted_at')
                    ->groupBy(['entity_id']),
                'rent_qry',
                function ($join) {
                    $join->on('leases.id', '=', 'rent_qry.entity_id');
                }
            )
            ->leftJoinSub(
                DB::table('lease_charges')
                    ->select('entity_id', DB::raw('SUM(annual) as service_charge_sum'))
                    ->whereIn('lease_charge_type', LeaseChargeType::SC_TYPES)
                    ->where('entity_type', Lease::class)
                    ->whereNull('deleted_at')
                    ->groupBy(['entity_id']),
                'service_charge_qry',
                function ($join) {
                    $join->on('leases.id', '=', 'service_charge_qry.entity_id');
                }
            )
            ->leftJoinSub(
                DB::table('lease_charges')
                    ->select('entity_id', DB::raw('SUM(annual) as insurance_charge_sum'))
                    ->whereIn('lease_charge_type', LeaseChargeType::INSURANCE_TYPES)
                    ->where('entity_type', Lease::class)
                    ->whereNull('deleted_at')
                    ->groupBy(['entity_id']),
                'insurance_charge_qry',
                function ($join) {
                    $join->on('leases.id', '=', 'insurance_charge_qry.entity_id');
                }
            )
            ->leftJoinSub(
                DB::table('lease_charges')
                    ->select('entity_id', DB::raw('SUM(annual) as rate_charge_sum'))
                    ->whereIn('lease_charge_type', LeaseChargeType::RATE_TYPES)
                    ->where('entity_type', Lease::class)
                    ->whereNull('deleted_at')
                    ->groupBy(['entity_id']),
                'rate_charge_qry',
                function ($join) {
                    $join->on('leases.id', '=', 'rate_charge_qry.entity_id');
                }
            )
            ->orderBy('properties.id');
    }
}
