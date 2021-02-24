<?php
namespace App\Modules\Lease\Repositories;

use App\Modules\Common\Models\Agent;
use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Common\Repositories\AgentRepository;

class LandlordRepository extends AgentRepository implements IYardiImport
{

    /**
     * @param Agent $model
     */
    public function __construct(Agent $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $data
     * @return Agent
     */
    public function importRecord(array $data)
    {
        $data['type'] = 'landlord';
        
        return parent::importRecord($data);
    }
}
