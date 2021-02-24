<?php
namespace App\Modules\Dashboard\Http\Controllers;

use App\Modules\Client\Repositories\ClientAccountRepository;
use App\Modules\Client\Repositories\PortfolioRepository;
use App\Modules\Dashboard\Services\EditDashboardService;
use App\Modules\Lease\Repositories\LeaseRepository;
use App\Modules\Lease\Repositories\TenantRepository;
use App\Modules\Property\Repositories\PropertyRepository;
use App\Modules\Property\Repositories\UnitRepository;

class EditDashboardController extends DashboardController
{

    /**
     * @var EditDashboardService
     */
    protected $service;

    /**
     * EditDashboardController constructor.
     * @param EditDashboardService $service
     */
    public function __construct(EditDashboardService $service)
    {
        $this->service = $service;
    }

    /**
     * @return array
     */
    public function getClientAccountReviewStats()
    {
        $repository = $this->makeClass(ClientAccountRepository::class);
        return $this->service->getReviewStatusByEntityType($repository->getModelTable());
    }

    /**
     * @return array
     */
    public function getPortfolioReviewStats()
    {
        $repository = $this->makeClass(PortfolioRepository::class);
        return $this->service->getReviewStatusByEntityType($repository->getModelTable());
    }

    /**
     * @return array
     */
    public function getPropertyReviewStats()
    {
        $repository = $this->makeClass(PropertyRepository::class);
        return $this->service->getReviewStatusByEntityType($repository->getModelTable());
    }

    /**
     * @return array
     */
    public function getUnitReviewStats()
    {
        $repository = $this->makeClass(UnitRepository::class);
        return $this->service->getReviewStatusByEntityType($repository->getModelTable());
    }

    /**
     * @return array
     */
    public function getLeaseReviewStats()
    {
        $repository = $this->makeClass(LeaseRepository::class);
        return $this->service->getReviewStatusByEntityType($repository->getModelTable());
    }

    /**
     * @return array
     */
    public function getTenantReviewStats()
    {
        $repository = $this->makeClass(TenantRepository::class);
        return $this->service->getReviewStatusByEntityType($repository->getModelTable());
    }

    /**
     * @return array
     */
    public function getPreviousWeekDailyEdits()
    {
        return $this->service->getDailyEditsApproval();
    }

    /**
     * @return array
     */
    public function getEditApprovalSplit()
    {
        return $this->service->getRejectedAcceptedEditSplit();
    }

    /**
     * @return array
     */
    public function getReviewedEditsTotal()
    {
        return $this->service->getReviewedEditsTotal();
    }
}
