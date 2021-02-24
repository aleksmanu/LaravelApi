<?php

namespace App\Modules\Lease\Repositories;

use App\Helpers\Helpers;
use App\Modules\Attachments\Traits\RepoHasAttachments;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Models\Address;
use App\Modules\Lease\Models\Lease;
use App\Modules\Lease\Models\LeaseChargeType;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;
use App\Modules\Common\Models\Agent;
use App\Modules\Lease\Models\LeaseBreak;
use App\Modules\Lease\Models\LeaseType;
use App\Modules\Lease\Models\Tenant;
use App\Modules\Lease\Repositories\LeaseBreakRepository;
use App\Modules\Lease\Repositories\LeaseReviewRepository;
use App\Modules\Property\Models\PropertyManager;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaseRepository
{
    use RepoHasAttachments;

    /**
     * @var Lease
     */
    protected $model;

    /**
     * @var LeaseBreakRepository
     */
    protected $leaseBreakRepository;

    /**
     * @var LeaseReviewRepository
     */
    protected $leaseReviewRepository;

    public function __construct(
        Lease $model,
        LeaseBreakRepository $leaseBreakRepository,
        LeaseReviewRepository $leaseReviewRepository
    ) {
        $this->model = $model;
        $this->leaseBreakRepository = $leaseBreakRepository;
        $this->leaseReviewRepository = $leaseReviewRepository;
    }

    private function query()
    {
        return $this->model->newQuery();
    }

    /**
     * @param bool $payable
     * @return Collection
     */
    public function getLeases($payable = false): Collection
    {
        $q = $this->query()
            ->select(
                $this->model->getTableName() . '.*',
                DB::raw('annual_rent_qry.annual_rent_sum as passing_rent_live'),
                DB::raw('annual_service_charge_qry.annual_service_charge_sum as service_charge_live'),
                DB::raw('annual_insurance_charge_qry.annual_insurance_charge_sum as insurance_charge_live'),
                DB::raw('annual_rate_charge_qry.annual_rate_charge_sum as rate_charge_live')
            )
            ->where('leases.payable', $payable)
            ->orderBy($this->model->getTableName() . '.id', 'asc')
            ->get();

        return $this->makeChargesSumQry($q)
            ->get();
    }

    /**
     * @param array $ids
     * @return Collection
     */
    public function find(array $ids): Collection
    {
        $q = $this->model->newQueryWithoutRelationships()
            ->select($this->model->getTableName() . '.id', $this->model->getTableName() . '.unit_id', $this->model->getTableName() . '.landlord_id', $this->model->getTableName() . '.cluttons_lease_ref', $this->model->getTableName() . '.lease_start', $this->model->getTableName() . '.lease_end', $this->model->getTableName() . '.holding', $this->model->getTableName() . '.live')
            ->where('leases.payable', true)
            ->with([
                'unit' => function ($query) {
                    $query->select('units.id', 'units.property_id', 'units.property_manager_id');
                },
                'unit.property' => function ($query) {
                    $query->select('id', 'name', 'yardi_property_ref', 'property_tenure_id')->without('attachments');
                },
                'unit.property.propertyTenure:id,name',
                'unit.propertyManager' => function ($query) {
                    $query->select('id', 'user_id')->without('units', 'realUnits');
                },
                'landlord:id,name'
            ])
            ->whereHas(
                'unit.property',
                function ($query) {
                    $query->where('live', '=', 1)->where('property_status_id', '!=', 6);
                }
            )
            ->whereIn($this->model->getTableName() . '.id', $ids);

        return $q->get();
    }

    /**
     * get receivable and payable total income data by valid data parameters
     *
     * @param array $params
     * @param bool $live
     * @return Collection
     */
    public function getSumValuesByValidData(array &$params, $live = true): Collection
    {
        if (isset($params['lease_outside_lt_act'])) {
            $params['outside_54_act'] = $params['lease_outside_lt_act'];
        }

        if (isset($params['lease_holding_over'])) {
            $params['holding'] = $params['lease_holding_over'];
        }

        $q = $this->model
            ->select(
                $this->model->getTableName() . '.payable',
                DB::raw('SUM(annual_rent_qry.annual_rent_sum) as annual_rent_sum'),
                DB::raw('SUM(annual_service_charge_qry.annual_service_charge_sum) as annual_service_charge_sum'),
                DB::raw('SUM(annual_insurance_charge_qry.annual_insurance_charge_sum) as annual_insurance_charge_sum'),
                DB::raw('SUM(annual_rate_charge_qry.annual_rate_charge_sum) as annual_rate_charge_sum')
            )
            ->with([
                'unit.property',
                'unit.propertyManager.user',
                'unit.property.propertyTenure'
            ])
            ->whereHas('unit.property', function ($query) {
                $query->where('live', '=', 1)
                    ->where('property_status_id', '!=', 6);
            })
            ->whereIn($this->model->getTableName() . '.id', function ($q) use ($params, $live) {
                $q->select($this->model->getTableName() . '.id')
                    ->from($this->model->getTableName())
                    ->leftJoin(Unit::getTableName(), $this->model->getTableName() . '.unit_id', '=', Unit::getTableName() . '.id')
                    ->leftJoin(Agent::getTableName() . ' as landlord', $this->model->getTableName() . '.landlord_id', '=', 'landlord.id')
                    ->leftJoin(Property::getTableName(), Unit::getTableName() . '.property_id', '=', Property::getTableName() . '.id')
                    ->leftJoin(Address::getTableName(), Property::getTableName() . '.address_id', '=', Address::getTableName() . '.id')
                    ->leftJoin(Portfolio::getTableName(), Property::getTableName() . '.portfolio_id', '=', Portfolio::getTableName() . '.id')
                    ->where($this->model->getTableName() . '.live', $live);

                $this->getQueryBuild($q, $params);
            });

        return $this->makeChargesSumQry($q)
            ->groupBy([$this->model->getTableName() . '.payable'])
            ->get();
    }

    /**
     * @param int $id
     * @return Lease
     */
    public function getLease(int $id): Lease
    {
        $lp = $this->query()
            ->select($this->model->getTableName() . '.*')
            ->with([
                'unit.property',
                'type',
                'tenants',
                'leaseCharges',
                'leaseTransactions',
            ])
            ->findOrFail($id);
        return $lp;
    }

    /**
     * @param array $params
     * @return Collection
     */
    public function identify(array $params, $payable = false, $live = true): Collection
    {
        $query = $this->model
            ->newQueryWithoutRelationships()
            ->select($this->model->getTableName() . '.id');

        $query->leftJoin(
            Unit::getTableName(),
            Unit::getTableName() . '.id',
            '=',
            Lease::getTableName() . '.unit_id'
        );
        $query->leftJoin(
            Agent::getTableName() . ' as landlord',
            'landlord.id',
            '=',
            Lease::getTableName() . '.landlord_id'
        );
        $query->leftJoin(
            Property::getTableName(),
            Property::getTableName() . '.id',
            '=',
            Unit::getTableName() . '.property_id'
        );
        $query->leftJoin(
            Address::getTableName(),
            Address::getTableName() . '.id',
            '=',
            Property::getTableName() . '.address_id'
        );
        $query->leftJoin(
            Portfolio::getTableName(),
            Portfolio::getTableName() . '.id',
            '=',
            Property::getTableName() . '.portfolio_id'
        );


        if (isset($params['lease_outside_lt_act'])) {
            $params['outside_54_act'] = $params['lease_outside_lt_act'];
        }

        if (isset($params['lease_holding_over'])) {
            $params['holding'] = $params['lease_holding_over'];
        }

        $this->getQueryBuild($query, $params);

        return $query
            ->where('leases.live', $live)
            ->where('leases.payable', $payable)
            ->get()->pluck('id');
    }

    /**
     * @param array $data
     * @return Lease
     */
    public function storeLeasePayable(array $data): Lease
    {
        return $this->model->create($data);
    }

    /**
     * @param bool $skip_get
     * @param array $filterData
     * @param string $sort_column
     * @param string $sort_order
     * @param integer $limit
     * @param integer $offset
     * @return
     */
    public function list(
        array $filterData = [],
        string $sort_column = 'live',
        string $sort_order = 'desc',
        int $limit = null,
        int $offset = null
    ) {
        $filteredResults = $this->model
            ->with(['unit.property.propertyTenure'])
            ->join(
                Unit::getTableName(),
                Lease::getTableName() . '.unit_id',
                Unit::getTableName() . '.id'
            );

        if (array_key_exists('unit_id', $filterData)) {
            $filterData[Unit::getTableName() . '.id'] = $filterData['unit_id'];
            unset($filterData['unit_id']);
        }

        if (array_key_exists('payable', $filterData)) {
            $filterData[Lease::getTableName() . '.payable'] = $filterData['payable'];
            unset($filterData['payable']);
        } else {
            $filterData[Lease::getTableName() . '.payable'] = false;
        }

        if (array_key_exists('property_id', $filterData)) {
            $filterData[Unit::getTableName() . '.property_id'] = $filterData['property_id'];
            unset($filterData['property_id']);
        }

        $filteredResults->where($filterData);

        $selectString = Lease::getTableName() . '.*';

        return collect([
            'row_count' => $filteredResults->count(),
            'rows' => $filteredResults
                ->skip($offset)
                ->take($limit)
                ->orderBy($sort_column, $sort_order)
                ->select($selectString)
                ->get(),
        ]);
    }

    public function search(string $searchTerm)
    {
        $lps = $this->model
            ->select([
                'leases.id as id',
                'properties.name as prop_name',
                'properties.id as prop_id',
                DB::raw("if (landlord.name is null, tenant.name, landlord.name) as lt_name"),
                DB::raw("if (leases.payable = 1, 'Payable', 'Receivable') as in_or_out"),
                'leases.cluttons_lease_ref as ref',
                'leases.lease_start as start',
                'leases.lease_end as end',
            ])->join('units', 'leases.unit_id', 'units.id')
            ->join('properties', 'units.property_id', 'properties.id')
            ->leftJoin('agents as landlord', 'leases.landlord_id', 'landlord.id')
            ->leftJoin('tenants as tenant', 'leases.id', 'tenant.lease_id')
            ->where('leases.cluttons_lease_ref', "like", "%$searchTerm%")
            ->where('properties.live', "=", 1);

        return $lps->orderBy('ref')->get();
    }

    /**
     * @param int $year_min
     * @param int $year_max
     * @param string $leaseType
     * @param array $params
     * @return mixed
     */
    public function getIncomeFilteredLeaseData(
        int $year_min,
        int $year_max,
        string $leaseType,
        array $params
    ) {
        /*
         * Select and get all leases that satisfy criteria - USES SQL CURSOR
         */
        $localModel = $this->model; // refactor artifact, don't ask
        // I won't ask

        $leaseQuery = $localModel
            ->select($localModel->getTableName() . '.*')
            ->with([
                'nextLeaseBreak',
                'leaseBreaks',
            ]);

        /*
         * Reference the filters from OG LeaseRepo
         */
        $leaseRepo = app()->make(LeaseRepository::class);
        $leaseRepo->applyIncomeFilters($leaseQuery, $localModel, $leaseType, $params, $year_min, $year_max);
        $leaseQuery->groupBy($localModel::getTableName() . '.id');

        $num_years = $year_max + 1;
        $income_vs_rent_review = array_fill(0, $num_years, ['leaseReviews' => []]);
        $income_vs_break_date = array_fill(0, $num_years, ['leaseBreaks' => []]);
        $income_vs_lease_expiry = array_fill(0, $num_years, ['leases' => [], 'income' => 0]);

        /*
        * Iterate through fetched leases and increment aggregates where appropriate
         */
        $lease_count = 0;
        $all_lease_ids_in_interval = [];
        $leaseQuery->chunk(
            500,
            function ($leases) use (
                &$income_vs_lease_expiry,
                &$income_vs_rent_review,
                &$income_vs_break_date,
                &$lease_count,
                &$all_lease_ids_in_interval
            ) {
                foreach ($leases as $lease) {
                    $all_lease_ids_in_interval[] = $lease->id;
                    $lease_count++;

                    if ($lease->lease_end) {
                        $year = (new Carbon($lease->lease_end))->year;
                        array_push($income_vs_lease_expiry[$year]['leases'], $lease->id);
                        $income_vs_lease_expiry[$year]['income'] += $lease->annual_rent;
                    }

                    if ($lease->next_review) {
                        $year = (new Carbon($lease->next_review))->year;
                        array_push($income_vs_rent_review[$year]['leaseReviews'], $lease->id);
                    }

                    foreach ($lease->leaseBreaks as $leaseBreak) {
                        $year = (new Carbon(($leaseBreak)->date))->year;
                        if ($leaseBreak->breakPartyOption->slug !== 'landlord-break-option') {
                            if ($leaseBreak->type === 'Date' || $leaseBreak->type === 'Anytime Before') {
                                $date = new Carbon(($leaseBreak)->date);
                                if ($date->subMonths($leaseBreak->min_notice) > Carbon::now()) {
                                    array_push($income_vs_break_date[$year]['leaseBreaks'], $leaseBreak->entity_id);
                                    break;
                                }
                            } else {
                                array_push($income_vs_break_date[$year]['leaseBreaks'], $leaseBreak->entity_id);
                                break;
                            }
                        }
                    }
                }
            }
        );

        $processedIncomeData = [
            'rent_review' => $income_vs_rent_review,
            'break_date' => $income_vs_break_date,
            'lease_expiry' => $income_vs_lease_expiry
        ];

        $response['lease_count'] = $lease_count;
        $response['yearly_income'] = $processedIncomeData;
        $response['lease_ids'] = $all_lease_ids_in_interval;
        $response['year_min'] = $year_min;
        $response['year_max'] = $year_max;

        return $response;
    }

    public function getIncomeFilteredOldestNewestLease(
        string $leaseType,
        array $params,
        string $tab
    ) {
        $income = $leaseType === 'income';
        if ($tab === 'break_date') {
            $localModel = app()->make(LeaseBreak::class);
            $leaseIds = Lease::where('leases.payable', !$income)->pluck('id');
            $lb = LeaseBreak::getTableName();
            $select = "MIN($lb.date) AS oldest, MAX($lb.date) as newest";
            $notDate = $localModel
                ->select('id')
                ->where('type', '!=', 'Date')
                ->whereIn('entity_id', $leaseIds)
                ->groupBy('entity_id')
                ->pluck('id');
            $notDate = $localModel
                ->selectRaw($select)
                ->whereIn('id', $notDate)
                ->first();
            $date = $localModel
                ->selectRaw('id')
                ->where('type', '=', 'Date')
                ->where('date', '>=', Carbon::now())
                ->whereIn('entity_id', $leaseIds)
                ->groupBy('entity_id')
                ->pluck('id');
            $date = $localModel
                ->selectRaw($select)
                ->whereIn('id', $date)
                ->first();
            $boundData = [
                'oldest' => $notDate['oldest'] < $date['oldest'] ? $notDate['oldest'] : $date['oldest'],
                'newest' => $notDate['newest'] > $date['newest'] ? $notDate['newest'] : $date['newest'],
            ];
        } else {
            $l = Lease::getTableName();
            if ($tab == 'rent_review') {
                $select = "MIN($l.next_review) AS oldest, MAX($l.next_review) as newest";
            } else {
                $select = "MIN($l.lease_end) AS oldest, MAX($l.lease_end) as newest";
            }
            $query = Lease::selectRaw($select)->where('leases.payable', !$income);
            $this->applyIncomeFilters($query, Lease::class, $leaseType, $params);
            $boundData = $query->get()[0];
        }

        $bounds['oldest'] = (new Carbon($boundData['oldest']))->year;
        $bounds['newest'] = (new Carbon($boundData['newest']))->year;

        return $bounds;
    }

    public function applyIncomeFilters(
        &$leaseQuery,
        $usedModel,
        string $leaseType,
        array $params,
        int $year_min = 0,
        int $year_max = 0
    ) {
        $leaseQuery->leftJoin(Unit::getTableName(), 'units.id', '=', $usedModel::getTableName() . '.unit_id');
        $leaseQuery->leftJoin(
            PropertyManager::getTableName(),
            'property_managers.id',
            '=',
            'units.property_manager_id'
        );
        $leaseQuery->leftJoin(Property::getTableName(), 'properties.id', '=', 'units.property_id');
        $leaseQuery->where('properties.live', '=', 1)->where('properties.property_status_id', '!=', 6);
        $leaseQuery->leftJoin(
            Address::getTableName(),
            Address::getTableName() . '.id',
            '=',
            Property::getTableName() . '.address_id'
        );
        $leaseQuery->leftJoin(Portfolio::getTableName(), 'portfolios.id', '=', 'properties.portfolio_id');
        if ($leaseType === 'expenditure') {
            $leaseQuery->leftJoin('agents as landlord', 'landlord.id', 'leases.landlord_id');
            $leaseQuery->where('leases.payable', true);
        } else {
            $leaseQuery->where('leases.payable', false);
        }

        if ($year_min && $year_max) {
            $leaseQuery->where($usedModel::getTableName() . '.lease_end', '>=', $year_min . '-01-01');
            $leaseQuery->where($usedModel::getTableName() . '.lease_end', '<=', $year_max . '-12-31');
        }

        if (isset($params['property_manager_id'])) {
            $leaseQuery->where(Property::getTableName() . '.property_manager_id', $params['property_manager_id']);
        }
        if (isset($params['property_live'])) {
            $leaseQuery->where(Property::getTableName() . '.live', $params['property_live']);
        }
        if (isset($params['property_use_id'])) {
            $leaseQuery->where(Property::getTableName() . '.property_use_id', $params['property_use_id']);
        }
        if (isset($params['property_tenure_id'])) {
            $leaseQuery->where(Property::getTableName() . '.property_tenure_id', $params['property_tenure_id']);
        }
        if (isset($params['property_category_id'])) {
            $leaseQuery->where(Property::getTableName() . '.property_category_id', $params['property_category_id']);
        }
        if (isset($params['conservation_area'])) {
            $leaseQuery->where(Property::getTableName() . '.conservation_area', $params['conservation_area']);
        }
        if (isset($params['listed_building'])) {
            $leaseQuery->where(Property::getTableName() . '.listed_building', $params['listed_building']);
        }
        if (isset($params['addr_town'])) {
            $leaseQuery->where(Address::getTableName() . '.town', 'LIKE', '%' . $params['addr_town'] . '%');
        }
        if (isset($params['unit_void']) && $params['unit_void'] === true) {
            $leaseQuery->doesntHave('unit.tenants');
        }
        if (isset($params['unit_fake']) && $params['unit_fake'] === true) {
            $leaseQuery->where(Unit::getTableName() . '.is_virtual', $params['unit_fake']);
        }

        if (isset($params['client_account_id'])) {
            $leaseQuery->where(Portfolio::getTableName() . '.client_account_id', $params['client_account_id']);
        }

        if (isset($params['lease_outside_lt_act']) && $leaseType === 'expenditure') {
            $leaseQuery->where($usedModel::getTableName() . '.outside_54_act', $params['lease_outside_lt_act']);
        }
        if (isset($params['lease_lt_name']) && $leaseType === 'expenditure') {
            $leaseQuery->where('landlord.name', 'LIKE', '%' . $params['lease_lt_name'] . '%');
        }
        if (isset($params['lease_holding_over']) && $leaseType === 'expenditure') {
            $leaseQuery->where($usedModel::getTableName() . '.holding', $params['lease_holding_over']);
        }

        return $leaseQuery;
    }

    /**
     * @param array $data
     * @return Lease
     */
    public function importRecord(array $data)
    {
        $data['payable'] = substr($data['cluttons_lease_ref'], -1) === 'H';
        $lease = $this->identify([
            'cluttons_lease_ref' => trim($data['cluttons_lease_ref'])
        ], $data['payable'])->first();

        $lease_review_repo = App::make(LeaseReviewRepository::class);
        $data['live'] = Helpers::convertStringToBool($data['live']);
        $data['outside_54_act'] = Helpers::convertStringToBool($data['outside_54_act']);
        $data['holding'] = Helpers::convertStringToBool($data['holding']);
        $data['turnover_rent'] = Helpers::convertStringToBool($data['turnover_rent']);
        $data['review'] = Helpers::convertStringToBool($data['review']);
        $data['review_initiatable_by_tenant'] = Helpers::convertStringToBool($data['review_initiatable_by_tenant']);
        $data['time_sensitive'] = Helpers::convertStringToBool($data['time_sensitive']);
        $data['notice_required'] = Helpers::convertStringToBool($data['notice_required']);
        $data['upwards_review_only'] = Helpers::convertStringToBool($data['upwards_review_only']);
        $data['interest_on_late_review'] = Helpers::convertStringToBool($data['interest_on_late_review']);
        $data['aga_required'] = Helpers::convertStringToBool($data['aga_required']);
        $data['keep_open_clause'] = Helpers::convertStringToBool($data['keep_open_clause']);
        $data['e_decorations_first'] = Helpers::convertStringToBool($data['e_decorations_first']);
        $data['e_decorations_last'] = Helpers::convertStringToBool($data['e_decorations_last']);
        $data['i_decorations_first'] = Helpers::convertStringToBool($data['i_decorations_first']);
        $data['i_decorations_last'] = Helpers::convertStringToBool($data['i_decorations_last']);

        $unit = Unit::where('yardi_import_ref', $data['unit_id'])->first();
        if ($unit) {
            $data['unit_id'] = $unit->id;
        }

        if (array_key_exists('tenant', $data)) {
            $tenant = trim($data['tenant']);
            $tenant = Tenant::where(
                'tenants.name',
                $tenant
            )->first();
        }

        $landlord = trim($data['landlord']);
        $landlord = Agent::where(
            'agents.name',
            $landlord
        )->where('type', 'landlord')->first();

        $managingAgent = trim($data['managing_agent']);
        $managingAgent = Agent::where(
            'agents.name',
            $managingAgent
        )->where('type', 'managing agent')->first();

        $data['landlord_id'] = $landlord ? $landlord->id : null;
        $data['managing_agent_id'] = $managingAgent ? $managingAgent->id : null;

        $type = LeaseType::where('name', $data['type'])->first();
        $data['type_id'] = $type->id;

        if ($lease) {
            $lease = $this->getLease($lease);
            $lease->leaseReviews()->delete();
            $lease->leaseBreaks()->delete();
            $lease->leaseCharges()->delete();
            $lease->chargeHistory()->delete();
            $lease->leaseTransactions()->delete();
            $lease->arrears()->delete();

            $lease->update($data);
            $lease = $lease->fresh();
        } else {
            $lease = $this->model->create($data);
        }
        $lease_review_repo->importRecord($lease->id);

        return $lease;
    }

    /**
     * common where builder
     *
     * @param $q
     * @param $params
     * @return mixed
     */
    private function getQueryBuild(&$q, $params)
    {
        foreach ($params as $key => $val) {
            if ($key === "id" || in_array($key, $this->model->fillable)) {
                $q->where($this->model->getTableName() . '.' . $key, $val);
            }
        }

        if (isset($params['property_manager_id'])) {
            $q->where(Property::getTableName() . '.property_manager_id', $params['property_manager_id']);
        }

        if (isset($params['property_live'])) {
            $q->where(Property::getTableName() . '.live', $params['property_live']);
        }

        if (isset($params['property_use_id'])) {
            $q->where(Property::getTableName() . '.property_use_id', $params['property_use_id']);
        }

        if (isset($params['property_tenure_id'])) {
            $q->where(Property::getTableName() . '.property_tenure_id', $params['property_tenure_id']);
        }

        if (isset($params['property_category_id'])) {
            $q->where(Property::getTableName() . '.property_category_id', $params['property_category_id']);
        }

        if (isset($params['conservation_area'])) {
            $q->where(Property::getTableName() . '.conservation_area', $params['conservation_area']);
        }

        if (isset($params['listed_building'])) {
            $q->where(Property::getTableName() . '.listed_building', $params['listed_building']);
        }

        if (isset($params['addr_town'])) {
            $q->where(Address::getTableName() . '.town', 'LIKE', '%' . $params['addr_town'] . '%');
        }

        if (isset($params['unit_void']) && $params['unit_void'] === true) {
            $q->doesntHave('unit.tenants');
        }

        if (isset($params['unit_fake']) && $params['unit_fake'] === true) {
            $q->where(Unit::getTableName() . '.is_virtual', $params['unit_fake']);
        }

        if (isset($params['client_account_id'])) {
            $q->where(Portfolio::getTableName() . '.client_account_id', $params['client_account_id']);
        }

        if (isset($params['portfolio_id'])) {
            $q->where(Portfolio::getTableName() . '.id', $params['portfolio_id']);
        }

        if (isset($params['lease_lt_name'])) {
            $q->where('landlord.name', 'LIKE', '%' . $params['lease_lt_name'] . '%');
        }

        return $q;
    }

    /**
     * Add lease charges sum query to get correct report data
     *
     * @param $q
     * @return mixed
     */
    private function makeChargesSumQry(&$q)
    {
        return $q
            ->leftJoinSub(
                DB::table('lease_charges')
                    ->select('entity_id', DB::raw('SUM(annual) as annual_rent_sum'))
                    ->whereIn('lease_charge_type', LeaseChargeType::RENT_TYPES)
                    ->where('entity_type', Lease::class)
                    ->whereNull('deleted_at')
                    ->groupBy(['entity_id']),
                'annual_rent_qry',
                function ($join) {
                    $join->on($this->model->getTableName() . '.id', '=', 'annual_rent_qry.entity_id');
                }
            )
            ->leftJoinSub(
                DB::table('lease_charges')
                    ->select('entity_id', DB::raw('SUM(annual) as annual_service_charge_sum'))
                    ->whereIn('lease_charge_type', LeaseChargeType::SC_TYPES)
                    ->where('entity_type', Lease::class)
                    ->whereNull('deleted_at')
                    ->groupBy(['entity_id']),
                'annual_service_charge_qry',
                function ($join) {
                    $join->on($this->model->getTableName() . '.id', '=', 'annual_service_charge_qry.entity_id');
                }
            )
            ->leftJoinSub(
                DB::table('lease_charges')
                    ->select('entity_id', DB::raw('SUM(annual) as annual_insurance_charge_sum'))
                    ->whereIn('lease_charge_type', LeaseChargeType::INSURANCE_TYPES)
                    ->where('entity_type', Lease::class)
                    ->whereNull('deleted_at')
                    ->groupBy(['entity_id']),
                'annual_insurance_charge_qry',
                function ($join) {
                    $join->on($this->model->getTableName() . '.id', '=', 'annual_insurance_charge_qry.entity_id');
                }
            )
            ->leftJoinSub(
                DB::table('lease_charges')
                    ->select('entity_id', DB::raw('SUM(annual) as annual_rate_charge_sum'))
                    ->whereIn('lease_charge_type', LeaseChargeType::RATE_TYPES)
                    ->where('entity_type', Lease::class)
                    ->whereNull('deleted_at')
                    ->groupBy(['entity_id']),
                'annual_rate_charge_qry',
                function ($join) {
                    $join->on($this->model->getTableName() . '.id', '=', 'annual_rate_charge_qry.entity_id');
                }
            );
    }
}
