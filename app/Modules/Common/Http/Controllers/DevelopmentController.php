<?php
namespace App\Modules\Common\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Common\Classes\UserImportProcessor;
use App\Modules\Common\Http\Requests\UserImportRequest;
use App\Modules\Auth\Repositories\Eloquent\UserRepository;
use App\Modules\Lease\Repositories\LeaseRepository;

class DevelopmentController extends Controller
{
    protected $user_repository;
    protected $lease_repository;

    public function __construct(
        UserRepository $userRepository,
        LeaseRepository $leaseRepository
    ) {
        $this->user_repository = $userRepository;
        $this->lease_repository = $leaseRepository;
    }

    public function exportUsers()
    {
        return response($this->user_repository->sheetExport());
    }

    public function importUsers(UserImportRequest $request, UserImportProcessor $import)
    {
        $valid = $request->validated();

        return response($this->user_repository->sheetImport($import, $valid['nukeFirst']));
    }

    public function clearCache()
    {
        try {
            \Cache::flush();
            return response()->json("You have your clean slate, now try not to mess it up.");
        } catch (\Exception $e) {
            return response($e);
        }
    }

    public function recalculateFinanceValues()
    {
        try {
            $leases = $this->lease_repository->getLeases();
            $leases = $leases->merge($this->lease_repository->getLeases(true));
            foreach ($leases as $lp) {
                $lp->passing_rent = $lp->rentCharges->sum('annual');
                $lp->service_charge = $lp->serviceCharges->sum('annual');
                $lp->insurance = $lp->insuranceCharges->sum('annual');
                $lp->rates_liability = $lp->rateCharges->sum('annual');
                $lp->save();
            }
            return response()->json("They've been recalculated. Hopefully");
        } catch (\Exception $e) {
            return response($e);
        }
    }
}
