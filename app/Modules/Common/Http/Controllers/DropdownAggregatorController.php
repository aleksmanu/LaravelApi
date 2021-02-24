<?php
namespace App\Modules\Common\Http\Controllers;

use App\Modules\Common\Http\Requests\DropdownAggregator\DropdownAggregatorDataRequest;
use App\Http\Controllers\Controller;
use App\Modules\Account\Repositories\AccountRepository;
use App\Modules\Client\Repositories\ClientAccountRepository;
use App\Modules\Client\Repositories\ClientAccountStatusRepository;
use App\Modules\Client\Repositories\OrganisationTypeRepository;
use App\Modules\Client\Repositories\PortfolioRepository;
use App\Modules\Common\Repositories\AddressRepository;
use App\Modules\Common\Repositories\CountryRepository;
use App\Modules\Common\Repositories\CountyRepository;
use App\Modules\Property\Repositories\LocationTypeRepository;
use App\Modules\Property\Repositories\MeasurementUnitRepository;
use App\Modules\Property\Repositories\PropertyCategoryRepository;
use App\Modules\Property\Repositories\PropertyManagerRepository;
use App\Modules\Property\Repositories\PropertyRepository;
use App\Modules\Property\Repositories\PropertyStatusRepository;
use App\Modules\Property\Repositories\PropertyTenureRepository;
use App\Modules\Property\Repositories\PropertyUseRepository;
use App\Modules\Property\Repositories\StopPostingRepository;

class DropdownAggregatorController extends Controller
{
    protected $accountRepository;
    protected $clientAccountStatusRepository;
    protected $clientAccountRepository;
    protected $propertyManagerRepository;
    protected $portfolioRepository;
    protected $propertyRepository;
    protected $addressRepository;
    protected $organisationTypeRepository;
    protected $countyRepository;
    protected $countryRepository;
    protected $propertyStatusRepository;
    protected $propertyUseRepository;
    protected $propertyTenureRepository;
    protected $locationTypeRepository;
    protected $propertyCategoryRepository;
    protected $stopPostingRepository;
    protected $measurementUnitRepository;

    public function __construct(
        AccountRepository $accountRepository,
        ClientAccountStatusRepository $clientAccountStatusRepository,
        ClientAccountRepository $clientAccountRepository,
        PropertyManagerRepository $propertyManagerRepository,
        PortfolioRepository $portfolioRepository,
        PropertyRepository $propertyRepository,
        AddressRepository $addressRepository,
        OrganisationTypeRepository $organisationTypeRepository,
        CountyRepository $countyRepository,
        CountryRepository $countryRepository,
        PropertyStatusRepository $propertyStatusRepository,
        PropertyUseRepository $propertyUseRepository,
        PropertyTenureRepository $propertyTenureRepository,
        LocationTypeRepository $locationTypeRepository,
        PropertyCategoryRepository $propertyCategoryRepository,
        StopPostingRepository $stopPostingRepository,
        MeasurementUnitRepository $measurementUnitRepository
    ) {
        $this->accountRepository = $accountRepository;
        $this->clientAccountStatusRepository = $clientAccountStatusRepository;
        $this->clientAccountRepository = $clientAccountRepository;
        $this->propertyManagerRepository = $propertyManagerRepository;
        $this->portfolioRepository = $portfolioRepository;
        $this->propertyRepository = $propertyRepository;
        $this->addressRepository = $addressRepository;
        $this->organisationTypeRepository = $organisationTypeRepository;
        $this->countyRepository = $countyRepository;
        $this->countryRepository = $countryRepository;
        $this->propertyStatusRepository = $propertyStatusRepository;
        $this->propertyUseRepository = $propertyUseRepository;
        $this->propertyTenureRepository = $propertyTenureRepository;
        $this->locationTypeRepository = $locationTypeRepository;
        $this->propertyCategoryRepository = $propertyCategoryRepository;
        $this->stopPostingRepository = $stopPostingRepository;
        $this->measurementUnitRepository = $measurementUnitRepository;
    }

    public function fetch(DropdownAggregatorDataRequest $request)
    {
        $return_data = [];

        if ($request->has('account_id')) {
            $return_data['accounts'] = $this->accountRepository->getAccounts(0);
        }

        if ($request->has('client_account_status_id')) {
            $return_data['client_account_statuses'] = $this->clientAccountStatusRepository->getClientAccountStatuses();
        }

        if ($request->has('client_account_id')) {
            $return_data['client_accounts'] = $this->clientAccountRepository->getClientAccounts(0, 0, 0);
        }

        if ($request->has('property_manager_id')) {
            $return_data['property_managers'] = $this->propertyManagerRepository->getPropertyManagers();
        }

        if ($request->has('portfolio_id')) {
            $return_data['portfolios'] = $this->portfolioRepository->getPortfolios();
        }

        if ($request->has('property_id')) {
            $return_data['properties'] = $this->propertyRepository->getProperties();
        }

        if ($request->has('address_id')) {
            $return_data['addresses'] = $this->addressRepository->getAddresses();
        }

        if ($request->has('organisation_type_id')) {
            $return_data['organisation_types'] = $this->organisationTypeRepository->getOrganisationTypes();
        }

        if ($request->has('county_id')) {
            $return_data['counties'] = $this->countyRepository->getCounties();
        }

        if ($request->has('country_id')) {
            $return_data['countries'] = $this->countryRepository->getCountries();
        }

        if ($request->has('property_status_id')) {
            $return_data['property_statuses'] = $this->propertyStatusRepository->getPropertyStatuses();
        }

        if ($request->has('property_use_id')) {
            $return_data['property_uses'] = $this->propertyUseRepository->getPropertyUses();
        }

        if ($request->has('property_tenure_id')) {
            $return_data['property_tenures'] = $this->propertyTenureRepository->getTenures();
        }

        if ($request->has('location_type_id')) {
            $return_data['location_types'] = $this->locationTypeRepository->getLocationTypes();
        }

        if ($request->has('property_category_id')) {
            $return_data['property_categories'] = $this->propertyCategoryRepository->getPropertyCategories();
        }

        if ($request->has('stop_posting_id')) {
            $return_data['stop_postings'] = $this->stopPostingRepository->getStopPostings();
        }

        if ($request->has('measurement_unit_id')) {
            $return_data['measurement_units'] = $this->measurementUnitRepository->getMeasurementUnits();
        }


        return response($return_data);
    }
}
