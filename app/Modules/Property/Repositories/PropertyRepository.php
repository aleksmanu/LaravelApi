<?php

namespace App\Modules\Property\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Modules\Attachments\Traits\RepoHasAttachments;
use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Models\Address;
use App\Modules\Common\Traits\HasServerSideSortingTrait;
use App\Modules\Common\Repositories\AddressRepository;
use App\Modules\Core\Classes\Repository;
use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Property\Models\LocationType;
use App\Modules\Property\Models\PropertyCategory;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Property\Models\PropertyStatus;
use App\Modules\Property\Models\PropertyTenure;
use App\Modules\Property\Models\PropertyUse;
use App\Modules\Property\Models\StopPosting;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Partner;

class PropertyRepository extends Repository implements IYardiImport
{
    use HasServerSideSortingTrait, RepoHasAttachments;

    /**
     * PropertyRepository constructor.
     * @param Property $model
     */
    public function __construct(Property $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $client_account_id
     * @param int $portfolio_id
     * @param int $property_manager_id
     * @param int $property_status_id
     * @param int $property_use_id
     * @param int $property_tenure_id
     * @param int $property_category_id
     * @param int $location_type_id
     * @return Collection
     */
    public function getProperties(
        int $client_account_id = 0,
        int $portfolio_id = 0,
        int $property_manager_id = 0,
        int $property_status_id = 0,
        int $property_use_id = 0,
        int $property_tenure_id = 0,
        int $property_category_id = 0,
        int $location_type_id = 0,
        bool $include_units = false
    ): Collection {
        $query = $this->model->query()->select(Property::getTableName() . '.*');
        if ($include_units) {
            $query->with(['units']);
        }
        $query = $this->applyFilters(
            $query,
            $client_account_id,
            $portfolio_id,
            $property_manager_id,
            $property_status_id,
            $property_use_id,
            $property_tenure_id,
            $property_category_id,
            $location_type_id
        );

        return $query->get();
    }

    /**
     * @param int $id
     * @return Property
     */
    public function getProperty(int $id): Property
    {
        $rentPerAnnum = $this->model->find($id)->units()->where('is_virtual', false)
            ->get()->sum('rentPerAnnum');

        $serviceChargePerAnnum = $this->model->find($id)->units()->where('is_virtual', false)
            ->get()->sum('serviceChargePerAnnum');

        $payableRentPerAnnum = $this->model->find($id)->leases()->where('is_virtual', true)
            ->where('live', '=', 1)
            ->get()->sum('annual_rent');

        $payableServiceChargePerAnnum = $this->model->find($id)->leases()->where('is_virtual', true)
            ->where('live', '=', 1)
            ->get()->sum('service_charge_per_annum');

        $payableInsurancePerAnnum = $this->model->find($id)->leases()->where('is_virtual', true)
            ->where('live', '=', 1)
            ->get()->sum('insurance_per_annum');

        $payableRatePerAnnum = $this->model->find($id)->leases()->where('is_virtual', true)
            ->where('live', '=', 1)
            ->get()->sum('rate_per_annum');

        $response = $this->model
            ->newQueryWithoutRelationships()
            ->select($this->model->getTableName() . '.*')
            ->withCount([
                'units',
                'leases',
                'units as realUnitCount' => function ($query) {
                    $query->where('is_virtual', false);
                },
            ])
            ->with([
                'leases' => function ($query) {
                    $query->where('live', '=',  '1');
                },
                'lettableUnits',
                'payableLeases',
                'receivableLeases',
                'reviewStatus',
                'lockedByUser',
                'address',
                'propertyManager',
                'propertyTenure',
                'propertyStatus',
                'propertyUse',
            ])
            ->findOrFail($id);
        $response->rentPerAnnum = $rentPerAnnum;
        $response->serviceChargePerAnnum = $serviceChargePerAnnum;
        $response->payableRentPerAnnum = $payableRentPerAnnum;
        $response->payableServiceChargePerAnnum = $payableServiceChargePerAnnum;
        $response->payableInsurancePerAnnum = $payableInsurancePerAnnum;
        $response->payableRatePerAnnum = $payableRatePerAnnum;

        return $response;
    }

    /**
     * @param array $params
     * @return Collection
     */
    public function identifyProperties(array $params): Collection
    {
        $query = $this->model
            ->newQueryWithoutRelationships()
            ->select($this->model->getTableName() . '.id');

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

        if (isset($params['property_id'])) {
            $params['id'] = $params['property_id'];
        }

        if (isset($params['property_live'])) {
            $params['live'] = $params['property_live'];
        } else {
            $params['live'] = true;
        }

        // if (isset($params['property_status'])) {
        //     $params['property_status_id'] = $params['property_status'];
        // }


        if (isset($params['client_account_id'])) {
            $query->where(Portfolio::getTableName() . '.client_account_id', $params['client_account_id']);
        }

        if (isset($params['addr_town'])) {
            $query->where(Address::getTableName() . '.town', 'LIKE', '%' . $params['addr_town'] . '%');
        }

        foreach ($params as $key => $val) {
            if ($key === "id" || in_array($key, $this->model->fillable)) {
                $query->where($this->model->getTableName() . '.' . $key, $val);
            }
        }

        return $query->get()->pluck('id');
    }

    /**
     * @param array $ids
     * @return Collection
     */
    public function findProperties(array $ids): Collection
    {
        return $this->model->newQueryWithoutRelationships()
            ->select($this->model->getTableName() . '.id', $this->model->getTableName() . '.portfolio_id', $this->model->getTableName() . '.address_id', $this->model->getTableName() . '.name', $this->model->getTableName() . '.yardi_property_ref', $this->model->getTableName() . '.yardi_property_ref')
            ->with([
                'address:id,postcode',
                'portfolio' => function ($query) {
                    $query->select('id', 'client_account_id');
                },
            ])
            ->withCount($this->model->withCount)
            ->whereIn($this->model->getTableName() . '.id', $ids)
            ->get();
    }

    /**
     * get properties data by valid data parameters
     *
     * @param array $params
     * @return Collection
     */
    public function findPropertiesByValidData(array &$params): Collection
    {
        if (isset($params['property_id'])) {
            $params['id'] = $params['property_id'];
        }

        if (isset($params['property_live'])) {
            $params['live'] = $params['property_live'];
        } else {
            $params['live'] = true;
        }

        return $this->model
            ->with(
                'reviewStatus',
                'lockedByUser',
                'portfolio'
            )
            ->withCount($this->model->withCount)
            ->whereIn($this->model->getTableName() . '.id', function ($q) use ($params) {
                $q->select($this->model->getTableName() . '.id')
                    ->from($this->model->getTableName())
                    ->leftJoin(Address::getTableName(), Address::getTableName() . '.id', '=', $this->model->getTableName() . '.address_id')
                    ->leftJoin(Portfolio::getTableName(), Portfolio::getTableName() . '.id', '=', $this->model->getTableName() . '.portfolio_id')
                    ->leftJoin(PropertyStatus::getTableName(), PropertyStatus::getTableName() . '.id', '=', $this->model->getTableName() . '.property_status_id')
                    ->where(PropertyStatus::getTableName() . '.slug', 'current-mgt');

                if (isset($params['client_account_id'])) {
                    $q->where(Portfolio::getTableName() . '.client_account_id', $params['client_account_id']);
                }

                if (isset($params['addr_town'])) {
                    $q->where(Address::getTableName() . '.town', 'LIKE', '%' . $params['addr_town'] . '%');
                }

                foreach ($params as $key => $val) {
                    if ($key === "id" || in_array($key, $this->model->fillable)) {
                        $q->where($this->model->getTableName() . '.' . $key, $val);
                    }
                }
            })
            ->get();
    }

    /**
     * @param array $data
     * @return Property
     */
    public function storeProperty(array $data): Property
    {
        $data['review_status_id'] = EloquentHelper::getRecordIdBySlug(
            ReviewStatus::class,
            ReviewStatus::NEVER_REVIEWED
        );
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Property
     */
    public function updateProperty(int $id, array $data): Property
    {
        $property = $this->model->find($id);
        $property->update($data);
        return $property;
    }

    /**
     * @param $base_query
     * @param int $client_account_id
     * @param int $portfolio_id
     * @param int $property_manager_id
     * @param int $property_status_id
     * @param int $property_use_id
     * @param int $property_tenure_id
     * @param int $property_category_id
     * @param int $location_type_id
     * @param string $property_name_partial
     * @return mixed
     */
    private function applyFilters(
        $base_query,
        int $client_account_id,
        int $portfolio_id,
        int $property_manager_id,
        int $property_status_id,
        int $property_use_id,
        int $property_tenure_id,
        int $property_category_id,
        int $location_type_id,
        $property_name_partial = ''
    ) {
        if ($client_account_id) {
            $base_query->whereHas('portfolio.clientAccount', function ($query) use ($client_account_id) {
                $query->where('id', $client_account_id);
            });
        }

        if ($portfolio_id) {
            $base_query->where($this->model->getTableName() . '.portfolio_id', $portfolio_id);
        }

        if ($property_manager_id) {
            $base_query->where('properties.property_manager_id', $property_manager_id);
        }

        if ($property_status_id) {
            $base_query->where('properties.property_status_id', $property_status_id);
        }

        if ($property_use_id) {
            $base_query->where('properties.property_use_id', $property_use_id);
        }

        if ($property_tenure_id) {
            $base_query->where('properties.property_tenure_id', $property_tenure_id);
        }

        if ($property_category_id) {
            $base_query->where('properties.property_category_id', $property_category_id);
        }

        if ($location_type_id) {
            $base_query->where('properties.location_type_id', $location_type_id);
        }

        if ($property_name_partial) {
            $base_query->where('properties.name', 'LIKE', '%' . $property_name_partial . '%');
        }

        return $base_query;
    }

    /**
     * @param int $id
     * @return Property
     * @throws \Exception
     */
    public function deleteProperty(int $id): Property
    {
        $property = $this->getProperty($id);
        $property->delete();
        return $property;
    }

    /**
     * @param string $sort_column
     * @param string $sort_order
     * @param int $offset
     * @param int $limit
     * @param int $client_account_id
     * @param int $portfolio_id
     * @param int $property_manager_id
     * @param int $property_status_id
     * @param int $property_use_id
     * @param int $property_tenure_id
     * @param int $property_category_id
     * @param int $location_type_id
     * @param $property_name_partial
     * @return Collection
     */
    public function getPropertyDataTable(
        string $sort_column,
        string $sort_order,
        int $offset,
        int $limit,
        int $client_account_id,
        int $portfolio_id,
        int $property_manager_id,
        int $property_status_id,
        int $property_use_id,
        int $property_tenure_id,
        int $property_category_id,
        int $location_type_id,
        $property_name_partial
    ): Collection {
        $query = $this->model->newQueryWithoutRelationships()
            ->select($this->model->getTableName() . '.id', $this->model->getTableName() . '.name', $this->model->getTableName() . '.yardi_property_ref', $this->model->getTableName() . '.address_id', $this->model->getTableName() . '.portfolio_id', $this->model->getTableName() . '.property_status_id', $this->model->getTableName() . '.property_manager_id', $this->model->getTableName() . '.property_tenure_id', $this->model->getTableName() . '.live')
            ->withCount([
                'units',
                'leases',
                'units as realUnitCount' => function ($query) {
                    $query->where('is_virtual', false);
                },
            ])
            ->with([
                'address:id,number,estate,suburb,town,postcode',
                'portfolio:id,client_account_id',
                'portfolio.clientAccount:id,name',
                'propertyStatus:id,name',
                'propertyManager' => function ($query) {
                    $query->select('id', 'user_id')->without('units', 'realUnits');
                },
                'propertyManager.user:id,first_name,last_name',
                'propertyTenure:id,name'
            ])
            ->without([
                'units'
            ]);

        $query = $this->applyFilters(
            $query,
            $client_account_id,
            $portfolio_id,
            $property_manager_id,
            $property_status_id,
            $property_use_id,
            $property_tenure_id,
            $property_category_id,
            $location_type_id,
            $property_name_partial
        );

        $query->groupBy(Property::getTableName() . '.id');

        // Get a copy of the query at this point with no limits applied, otherwise total count will be skewed
        // Insert the monster query inside a simple count() query. Replace bindings first
        $countSelect = 'SELECT count(*) as row_count FROM (' . $query->toSql() . ') AS T1';
        $bindParams  = $query->getBindings(); //keep a hold of these before sort and limit is added

        $this->johnifySortColumn($sort_column);

        $query->skip($offset)
            ->take($limit)
            ->orderBy($sort_column, $sort_order);

        return collect([
            'row_count' => \DB::select($countSelect, $bindParams)[0]->row_count,
            'rows'      => $query->get()
        ]);
    }

    /**
     * @param array $data
     * @return Property|mixed
     */
    public function importRecord(array $data)
    {
        $portfolio        = Portfolio::where('yardi_portfolio_ref', $data['estate_ref'])->first();
        $tenure           = PropertyTenure::where('name', $data['client_tenure'])->first();
        $location_type    = LocationType::where('name', $data['location_type'])->first();
        $use              = PropertyUse::where('name', $data['main_use'])->first();
        $category         = PropertyCategory::where('name', $data['category'])->first();
        $status           = PropertyStatus::where('name', $data['status'])->first();
        $stop_posting     = StopPosting::where('name', $data['stop_posting'])->first();
        $property_manager = User::where('first_name', $data['first_name'])
            ->where('last_name', $data['last_name'])
            ->first()->propertyManager;

        $partner = Partner::where('name', $data['partner'])->first();
        if (!$partner) {
            $partner = Partner::create([
                'name' => $data['partner'],
            ]);
        };
        $partner = $partner->fresh();

        $address_repository = \App::make(AddressRepository::class);
        $property = $this->identifyProperties([
            'yardi_property_ref' => $data['property_ref']
        ])->first();
        if ($property) {
            $property = $this->getProperty($property);
            $address = $address_repository->importRecord($data, $property->address->id);
        } else {
            $address = $address_repository->importRecord($data);
        }

        $listed_building   = \Helpers::convertStringToBool($data['listed_building']);
        $live              = \Helpers::convertStringToBool($data['live']);
        $conservation_area = \Helpers::convertStringToBool($data['conservation_area']);
        $air_con           = \Helpers::convertStringToBool($data['air_conditioned']);
        $vat_registered    = \Helpers::convertStringToBool($data['vat_registered']);
        $approved          = \Helpers::convertStringToBool($data['approved']);

        $data = [
            'property_manager_id'       => $property_manager->id,
            'portfolio_id'              => $portfolio->id,
            'address_id'                => $address->id,
            'property_status_id'        => $status->id,
            'property_use_id'           => $use ? $use->id : null,
            'property_tenure_id'        => $tenure->id,
            'location_type_id'          => $location_type->id,
            'property_category_id'      => $category ? $category->id : null,
            'stop_posting_id'           => $stop_posting->id,
            'name'                      => $data['property_name'],
            'yardi_property_ref'        => $data['property_ref'],
            'yardi_alt_ref'             => $data['alt_ref'],
            'total_lettable_area'       => $data['total_lettable_area'],
            'void_total_lettable_area'  => $data['void_total_lettable_area'],
            'total_site_area'           => $data['total_site_area'],
            'total_gross_internal_area' => $data['total_gross_internal_area'],
            'total_rateable_value'      => $data['total_rateable_value'],
            'void_total_rateable_value' => $data['void_total_rateable_value'],
            'listed_building'           => $listed_building,
            'live'                      => $live,
            'partner_id'                => $partner->id,
            'conservation_area'         => $conservation_area,
            'air_conditioned'           => $air_con,
            'vat_registered'            => $vat_registered,
            'approved'                  => $approved,
            'approved_initials'         => $data['approved_by'],
            'approved_at'               => $data['approved_date'] ? Carbon::parse($data['approved_date']) : null,
            'held_initials'             => $data['held_initials'],
            'held_at'                   => $data['held_date'] ? Carbon::parse($data['held_date']) : null,
        ];

        if ($property) {
            return $property->update($data);
        } else {
            return $this->storeProperty($data);
        }
    }

    /**
     * @param string $searchTerm
     * @return Property|mixed
     * @throws \Exception
     */
    public function searchForRef($searchTerm)
    {
        $propertyRef = $this->model->where(
            'properties.yardi_property_ref',
            $searchTerm
        )
            ->leftJoin('property_statuses', 'properties.property_status_id', 'property_statuses.id')
            ->where('property_statuses.slug', '!=', 'sold')
            ->with('portfolio.clientAccount');

        $altRef = $this->model->where(
            'properties.yardi_alt_ref',
            $searchTerm
        )
            ->leftJoin('property_statuses', 'properties.property_status_id', 'property_statuses.id')
            ->where('property_statuses.slug', '!=', 'sold')
            ->with('properties.portfolio.clientAccount');

        $propertyRefPartial = $this->model->where(
            'properties.yardi_property_ref',
            'LIKE',
            "%{$searchTerm}%"
        )
            ->leftJoin('property_statuses', 'properties.property_status_id', 'property_statuses.id')
            ->where('property_statuses.slug', '!=', 'sold')
            ->with('portfolio.clientAccount');

        $altRefPartial = $this->model->where(
            'properties.yardi_alt_ref',
            'LIKE',
            "%{$searchTerm}%"
        )
            ->leftJoin('property_statuses', 'properties.property_status_id', 'property_statuses.id')
            ->where('property_statuses.slug', '!=', 'sold')
            ->with('portfolio.clientAccount');

        return $propertyRef
            ->union($altRef)
            ->union($propertyRefPartial)
            ->union($altRefPartial)
            ->get();
    }

    /**
     * And I heard a voice in the midst of the four beasts
     * And I looked, and behold a pale horse
     * And his name that sat on him was death, and hell followed with him.
     *
     * @param string $searchTerm
     * @return Property|mixed
     * @throws \Exception
     */
    public function searchForAddress($searchTerm)
    {
        $searchTerm = explode(' ', $searchTerm);
        $result = [];
        $newResult = [];
        $this->allPermutations($searchTerm, "", $result);

        for ($i = 0; $i < sizeof($result); $i++) {
            $temp = explode(" ", $result[$i]);
            array_shift($temp);
            if (sizeof($temp) >= sizeof($searchTerm)) {
                $newResult[] = implode('%', $temp);
            }
        }

        $query = $this->model
            ->with(['portfolio.clientAccount'])
            ->join('addresses', 'properties.address_id', 'addresses.id')
            ->leftJoin('countries', 'addresses.country_id', 'countries.id')
            ->leftJoin('counties', 'addresses.county_id', 'counties.id')
            ->leftJoin('property_statuses', 'properties.property_status_id', 'property_statuses.id');
        foreach ($newResult as $term) {
            $query = $query->orWhere(
                \DB::raw('CONCAT_WS(
                    " ",
                    addresses.unit,
                    addresses.building,
                    addresses.number,
                    addresses.street,
                    addresses.estate,
                    addresses.suburb,
                    addresses.town,
                    addresses.postcode,
                    counties.name,
                    countries.name
                )'),
                'LIKE',
                "%{$term}%"
            );
        }
        $query->where('property_statuses.slug', '!=', 'sold');
        return $query->orderBy('properties.id')->get();
    }


    private function allPermutations($arr, $temp_string, &$collect)
    {
        if ($temp_string != "") {
            $collect[] = $temp_string;
        }

        for ($i = 0; $i < sizeof($arr); $i++) {
            $arrcopy = $arr;
            $elem = array_splice($arrcopy, $i, 1); // removes and returns the i'th element
            if (sizeof($arrcopy) > 0) {
                $this->allPermutations($arrcopy, $temp_string . " " . $elem[0], $collect);
            } else {
                $collect[] = $temp_string . " " . $elem[0];
            }
        }
    }
}
