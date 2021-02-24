<?php

namespace App\Modules\Property\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Modules\Attachments\Traits\RepoHasAttachments;
use App\Modules\Core\Classes\Repository;
use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Models\ReviewStatus;
use App\Modules\Property\Models\MeasurementUnit;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\PropertyManager;
use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Models\Portfolio;
use App\Modules\Common\Models\Address;
use App\Modules\Common\Traits\HasServerSideSortingTrait;
use App\Modules\Property\Models\Unit;

class UnitRepository extends Repository implements IYardiImport
{
    use HasServerSideSortingTrait;
    use RepoHasAttachments;

    /**
     * UnitRepository constructor.
     * @param Unit $model
     */
    public function __construct(Unit $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getUnits(): Collection
    {
        return $this->model
            ->select(Unit::getTableName() . '.*')
            ->with('property.address')
            ->where(Unit::getTableName() . '.is_virtual', false)
            ->orderBy(Unit::getTableName() . '.name', 'asc')->get();
    }

    /**
     * @param $id
     * @return Unit
     */
    public function getUnit($id): Unit
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->with(
                'property.portfolio.clientAccount',
                'property.address',
                'propertyManager.user',
                'measurementUnit',
                'leases',
                'reviewStatus',
                'uniqueTenants',
                'lockedByUser'
            )
            ->withCount(['uniqueTenants'])
            ->findOrFail($id);
    }

    /**
     * @param array $ids
     * @return Collection
     */
    public function findUnits(array $ids): Collection
    {
        return $this->model
            ->select($this->model->getTableName() . '.*')
            ->with(
                'property.address',
                'property.portfolio.clientAccount',
                'propertyManager.user'
            )
            ->whereIn($this->model->getTableName() . '.id', $ids)
            ->get();
    }

    public function dashOptimizedFind(array $ids) //: Collection
    {
        return \DB::table('units')
            ->select([
                'units.id as unit_id',
                'units.demise as unit_name',
                'properties.name as property_name',
                'units.yardi_unit_ref as unit_ref',
                'properties.yardi_property_ref as property_ref',
                'addresses.postcode as postcode',
                'accounts.name as client_name',
                'leases.id as lease_id',
            ])
            ->leftJoin('properties', 'properties.id', 'units.property_id')
            ->leftJoin('addresses', 'addresses.id', 'properties.address_id')
            ->leftJoin('portfolios', 'portfolios.id', 'properties.portfolio_id')
            ->leftJoin('client_accounts', 'client_accounts.id', 'portfolios.client_account_id')
            ->leftJoin('accounts', 'accounts.id', 'client_accounts.account_id')
            ->leftJoin('leases', 'leases.unit_id', 'units.id')
            ->whereIn('units.id', $ids)
            ->get();
    }

    /**
     * @param array $data
     * @return Unit
     */
    public function storeUnit(array $data): Unit
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
     * @return Unit
     */
    public function updateUnit(int $id, array $data): Unit
    {
        $unit = $this->getUnit($id);
        $unit->update($data);
        return $unit;
    }

    /**
     * @param int $id
     * @return Unit
     * @throws \Exception
     */
    public function deleteUnit(int $id): Unit
    {
        $unit = $this->getUnit($id);
        $unit->delete();
        return $unit;
    }

    /**
     * @param string $sort_column
     * @param string $sort_order
     * @param int $offset
     * @param int $limit
     * @param int $property_id
     * @param int $property_manager_id
     * @param int $client_account_id
     * @param int $portfolio_id
     * @param $property_name_partial
     * @return Collection
     */
    public function getUnitsDataTable(
        string $sort_column,
        string $sort_order,
        int $offset,
        int $limit,
        int $property_id,
        int $property_manager_id,
        int $client_account_id,
        int $portfolio_id,
        $property_name_partial
    ): Collection {
        $query  = $this->model->select(Unit::getTableName() . '.id', Unit::getTableName() . '.demise', Unit::getTableName() . '.yardi_unit_ref', Unit::getTableName() . '.unit', Unit::getTableName() . '.property_id', Unit::getTableName() . '.property_manager_id');

        $query->with([
            'property:id,name,portfolio_id',
            'property.portfolio:id,name,client_account_id',
            'property.portfolio.clientAccount:id,name',
            'propertyManager' => function ($query) {
                $query->select('id', 'user_id')->without('units', 'realUnits');
            },
            'propertyManager.user:id,first_name,last_name',
            'leases' => function ($query) {
                $query->select('id', 'lease_status', 'unit_id')->without('attachments', 'chargeHistory', 'leaseBreaks', 'leaseReviews', 'nextLeaseBreak');
            }
        ])->groupBy(Unit::getTableName() . '.id');

        $query = $this->applyFilters(
            $query,
            $property_id,
            $property_manager_id,
            $client_account_id,
            $portfolio_id,
            $property_name_partial
        );
        $query->where(Unit::getTableName() . '.is_virtual', false);

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
     * @param array $params
     * @return Collection
     */
    public function identifyUnits(array $params): Collection
    {
        $query = $this->model
            ->newQueryWithoutRelationships()
            ->select($this->model->getTableName() . '.id');

        $query->leftJoin(
            Property::getTableName(),
            Property::getTableName() . '.id',
            '=',
            Unit::getTableName() . '.property_id'
        );
        $query->leftJoin(
            Portfolio::getTableName(),
            Portfolio::getTableName() . '.id',
            '=',
            Property::getTableName() . '.portfolio_id'
        );
        $query->leftJoin(
            Address::getTableName(),
            Address::getTableName() . '.id',
            '=',
            Property::getTableName() . '.address_id'
        );

        if (isset($params['unit_id'])) {
            $params['id'] = $params['unit_id'];
        }

        if (isset($params['unit_void']) && $params['unit_void'] === true) {
            $query->doesntHave('tenants');
        }
        if (isset($params['unit_fake']) && $params['unit_fake'] === true) {
            $params['is_virtual'] = $params['unit_fake'];
        }

        if (isset($params['property_live'])) {
            $query->where(Property::getTableName() . '.live', $params['property_live']);
        }
        if (isset($params['property_use_id'])) {
            $query->where(Property::getTableName() . '.property_use_id', $params['property_use_id']);
        }
        if (isset($params['property_tenure_id'])) {
            $query->where(Property::getTableName() . '.property_tenure_id', $params['property_tenure_id']);
        }
        if (isset($params['property_category_id'])) {
            $query->where(Property::getTableName() . '.property_category_id', $params['property_category_id']);
        }
        if (isset($params['conservation_area'])) {
            $query->where(Property::getTableName() . '.conservation_area', $params['conservation_area']);
        }
        if (isset($params['listed_building'])) {
            $query->where(Property::getTableName() . '.listed_building', $params['listed_building']);
        }
        if (isset($params['addr_town'])) {
            $query->where(Address::getTableName() . '.town', 'LIKE', '%' . $params['addr_town'] . '%');
        }

        if (isset($params['client_account_id'])) {
            $query->where(Portfolio::getTableName() . '.client_account_id', $params['client_account_id']);
        }

        foreach ($params as $key => $val) {
            if ($key === "id" || in_array($key, $this->model->fillable)) {
                $query->where($this->model->getTableName() . '.' . $key, $val);
            }
        }

        return $query->get()->pluck('id');
    }

    private function applyFilters(
        $base_query,
        int $property_id,
        int $property_manager_id,
        int $client_account_id,
        int $portfolio_id,
        $property_name_partial = ''
    ) {

        if ($property_id) {
            $base_query->whereHas('property', function ($query) use ($property_id) {
                $query->where('id', '=', $property_id);
            });
        }

        if ($client_account_id) {
            $base_query->whereHas('property.portfolio', function ($query) use ($client_account_id) {
                $query->where('client_account_id', '=', $client_account_id);
            });
        }

        if ($portfolio_id) {
            $base_query->whereHas('property.portfolio', function ($query) use ($portfolio_id) {
                $query->where('id', '=', $portfolio_id);
            });
        }

        if ($property_manager_id) {
            $base_query->where('units.property_manager_id', $property_manager_id);
        }

        if ($property_name_partial) {
            /**
             * A lot of units don't have a name at all
             */
            $base_query->where(function ($query) use ($property_name_partial) {
                $query->where(Property::getTableName() . '.name', 'LIKE', '%' . $property_name_partial . '%')
                    ->orWhere(Unit::getTableName() . '.name', 'LIKE', '%' . $property_name_partial . '%')
                    ->orWhere(Unit::getTableName() . '.demise', 'LIKE', '%' . $property_name_partial . '%');
            });
        }

        return $base_query;
    }

    /**
     * @param array $data
     * @return Unit
     */
    public function importRecord(array $data)
    {
        $property         = Property::where('yardi_property_ref', $data['property_ref'])->first();
        $property_manager = User::where('users.first_name', $data['first_name'])
            ->where('users.last_name', $data['last_name'])
            ->first()
            ->propertyManager;
        $measurement_unit = MeasurementUnit::where('name', $data['dimensions'])->first();
        $approved         = \Helpers::convertStringToBool($data['approved']);
        $is_virtual       = \Helpers::convertStringToBool($data['nonlettable']);

        // We've had issues where they've sent us units/leases/charges and so on that don't have a
        // matching parent record either because they're `terminated` or something similar.
        if (!$property) {
            return;
        }

        if ($is_virtual) {
            $property_manager = $property->propertyManager;
        }

        $unit = $this->identifyUnits(['yardi_import_ref' => $data['id']])->first();
        $data = [
            'property_id'             => $property->id,
            'property_manager_id'     => $property_manager->id,
            'measurement_unit_id'     => $measurement_unit->id,
            'yardi_import_ref'        => $data['id'],
            'yardi_property_unit_ref' => $data['property_unit_id'],
            'demise'                  => $data['demise'],
            'yardi_unit_ref'          => $data['unit_ref'],
            'name'                    => $data['name'],
            'measurement_value'       => $data['property_area'],
            'approved'                => $approved,
            'approved_initials'       => $data['approved_by'],
            'approved_at'             => $data['approved_date'] ? Carbon::parse($data['approved_date']) : null,
            'held_initials'           => $data['held_initials'],
            'held_at'                 => $data['held_date'] ? Carbon::parse($data['held_date']) : null,
            'is_virtual'              => $is_virtual,
        ];

        if ($unit) {
            $unit = $this->getUnit($unit);
            return $unit->update($data);
        } else {
            return $this->storeUnit($data);
        }
    }
}
