<?php
namespace App\Modules\Search\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Modules\Property\Repositories\PropertyRepository;
use App\Modules\Acquisition\Repositories\AcquisitionRepository;
use App\Modules\Lease\Repositories\TenantRepository;
use App\Modules\Workorder\Repositories\WorkOrderRepository;
use App\Modules\Search\Http\Requests\SearchRequest;
use App\Modules\Lease\Repositories\LeaseRepository;
use App\Modules\Common\Repositories\AgentRepository;

class SearchController extends Controller
{
    private $propertyRepository;
    private $acquisitionRepository;
    private $tenantRepository;
    private $workOrderRepository;
    private $leaseRepository;
    private $agentRepository;

    public function __construct(
        PropertyRepository $propertyRepository,
        AcquisitionRepository $acquisitionRepository,
        TenantRepository $tenantRepository,
        WorkOrderRepository $workOrderRepository,
        LeaseRepository $leaseRepository,
        AgentRepository $agentRepository
    ) {
        $this->propertyRepository = $propertyRepository;
        $this->acquisitionRepository = $acquisitionRepository;
        $this->tenantRepository = $tenantRepository;
        $this->workOrderRepository = $workOrderRepository;
        $this->leaseRepository = $leaseRepository;
        $this->agentRepository = $agentRepository;
    }

    public function search(SearchRequest $request)
    {
        $toReturn = [
            'address'             => [],
            'prop_ref'            => [],
            'tenant'              => [],
            'pop_area'            => [],
            'wo_ref'              => [],
            'lease_ref'           => [],
            'landlord'            => [],
            'site_surveyor'       => [],
            'site_landlord'       => [],
            'acquisition_address' => [],
            'acquisition_city'    => [],
            'site_reference'      => [],
            'site_type'           => [],
            'planning_app_number' => [],
            'sales_force'         => [],
        ];
        $search = preg_replace('/\s+/', ' ', $request->search);
        switch ($request->scope) {
            case 'global':
                try {
                    $toReturn['address'] = $this->propertyRepository->searchForAddress($search);
                } catch (\Exception $e) {
                }

                try {
                    $toReturn['prop_ref'] = $this->propertyRepository->searchForRef($search);
                } catch (\Exception $e) {
                }

                $toReturn['tenant'] = $this->tenantRepository->search($search);

                $toReturn['wo_ref'] = $this->workOrderRepository->search($search);

                $toReturn['lease_ref'] = $this->leaseRepository->search($search);

                $toReturn['landlord'] = $this->agentRepository->search($search);
                break;
            case 'address':
                try {
                    $toReturn[$request->scope] = $this->propertyRepository->searchForAddress($search);
                } catch (\Exception $e) {
                }
                break;
            case 'prop_ref':
                try {
                    $toReturn[$request->scope] = $this->propertyRepository->searchForRef($search);
                } catch (\Exception $e) {
                }
                break;
            case 'tenant':
                $toReturn[$request->scope] = $this->tenantRepository->search($search);
                break;
            case 'pop_area':
                $toReturn[$request->scope] = $this->acquisitionRepository->search($search);
                break;
            case 'wo_ref':
                $toReturn[$request->scope] = $this->workOrderRepository->search($search);
                break;
            case 'lease_ref':
                $toReturn[$request->scope] = $this->leaseRepository->search($search);
                break;
            case 'landlord':
                $toReturn[$request->scope] = $this->agentRepository->search($search);
                break;
            case 'site_surveyor':
                $toReturn[$request->scope] = $this->acquisitionRepository->surveyorSearch($search);
                break;
            case 'site_landlord':
                $toReturn[$request->scope] = $this->acquisitionRepository->landlordSearch($search);
                break;
            case 'site_reference':
                $toReturn[$request->scope] = $this->acquisitionRepository->referenceSearch($search);
                break;
            case 'acquisition_city':
                $toReturn[$request->scope] = $this->acquisitionRepository->citySearch($search);
                break;
            case 'planning_app_number':
                $toReturn[$request->scope] = $this->acquisitionRepository->appNumberSearch($search);
                break;
            case 'site_type':
                $toReturn[$request->scope] = $this->acquisitionRepository->appTypeSearch($search);
                break;
            case 'acquisition_address':
                $toReturn[$request->scope] = $this->acquisitionRepository->addressSearch($search);
                break;
            default:
                return response('Unknown scope. Please try again', 422);
        }
        return response($toReturn);
    }
}
