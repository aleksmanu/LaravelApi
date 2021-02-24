<?php
namespace App\Modules\Common\Repositories;

use App\Modules\Common\Models\Agent;
use App\Modules\Core\Classes\Repository;
use App\Modules\Core\Interfaces\IYardiImport;
use Illuminate\Support\Collection;

class AgentRepository extends Repository implements IYardiImport
{

    /**
     * @param Agent $model
     */
    public function __construct(Agent $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getLandlords(): Collection
    {
        return $this->model->where('type', 'landlord')->get();
    }

    /**
     * @return Collection
     */
    public function getManagingAgents(): Collection
    {
        return $this->model->where('type', 'managing agent')->get();
    }

    /**
     * @param $id
     * @return Agent
     */
    public function getAgent($id): Agent
    {
        return $this->model->findOrFail($id);
    }

    public function search($searchTerm)
    {
        return $this->model
            ->select([
                'properties.name as prop_name',
                'properties.id as prop_id',
                'agents.name as lt_name',
                'leases.cluttons_lease_ref as ref',
                'leases.lease_start as start',
                'leases.lease_end as end'
            ])->join('leases', 'leases.landlord_id', 'agents.id')
            ->join('units', 'leases.unit_id', 'units.id')
            ->join('properties', 'units.property_id', 'properties.id')
            ->where('agents.name', "LIKE", "%{$searchTerm}%")->get();
    }

    /**
     * @param array $data
     * @return Agent
     */
    public function importRecord(array $data)
    {
        $address_repository = \App::make(AddressRepository::class);

        $agent = $this->model->where([
            'name' => trim($data['name']),
            'type' => trim($data['type']),
        ])->first();
        $address = $address_repository->importRecord($data, ($agent ? $agent->address->id : null));

        $data['address_id'] = $address ? $address->id : null;
        $data['name'] = trim($data['name']);

        if ($agent) {
            return $agent->update($data);
        } else {
            return Agent::create($data);
        }
    }
}
