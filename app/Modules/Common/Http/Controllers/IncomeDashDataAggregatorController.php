<?php

namespace App\Modules\Common\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Common\Http\Requests\IncomeDashDataAggregator\IncomeDashFetchFilterOptionsRequest;
use App\Modules\Client\Repositories\ClientAccountRepository;
use App\Modules\Client\Repositories\PortfolioRepository;
use App\Modules\Common\Http\Resources\DashboardPortfolioResource;
use App\Modules\Common\Http\Resources\DashboardPropertyResource;
use App\Modules\Lease\Repositories\LeaseRepository;
use App\Modules\Property\Models\Unit;
use App\Modules\Property\Repositories\LocationTypeRepository;
use App\Modules\Property\Repositories\PartnerRepository;
use App\Modules\Property\Repositories\PropertyCategoryRepository;
use App\Modules\Property\Repositories\PropertyManagerRepository;
use App\Modules\Property\Repositories\PropertyRepository;
use App\Modules\Property\Repositories\PropertyStatusRepository;
use App\Modules\Property\Repositories\PropertyTenureRepository;
use App\Modules\Property\Repositories\PropertyUseRepository;
use App\Modules\Property\Repositories\UnitRepository;

class IncomeDashDataAggregatorController extends Controller
{
    protected $clientAccountRepository;
    protected $propertyManagerRepository;
    protected $portfolioRepository;
    protected $propertyStatusRepository;
    protected $propertyUseRepository;
    protected $propertyTenureRepository;
    protected $locationTypeRepository;
    protected $propertyCategoryRepository;
    protected $leaseRepository;
    protected $propertyRepository;
    protected $unitRepository;
    protected $partnerRepository;

    public function __construct(
        ClientAccountRepository $clientAccountRepository,
        PropertyManagerRepository $propertyManagerRepository,
        PortfolioRepository $portfolioRepository,
        PropertyStatusRepository $propertyStatusRepository,
        PropertyUseRepository $propertyUseRepository,
        PropertyTenureRepository $propertyTenureRepository,
        LocationTypeRepository $locationTypeRepository,
        PropertyCategoryRepository $propertyCategoryRepository,
        LeaseRepository $leaseRepository,
        PropertyRepository $propertyRepository,
        UnitRepository $unitRepository,
        PartnerRepository $partnerRepository
    ) {
        $this->clientAccountRepository = $clientAccountRepository;
        $this->propertyManagerRepository = $propertyManagerRepository;
        $this->portfolioRepository = $portfolioRepository;
        $this->propertyStatusRepository = $propertyStatusRepository;
        $this->propertyUseRepository = $propertyUseRepository;
        $this->propertyTenureRepository = $propertyTenureRepository;
        $this->locationTypeRepository = $locationTypeRepository;
        $this->propertyCategoryRepository = $propertyCategoryRepository;
        $this->leaseRepository = $leaseRepository;
        $this->propertyRepository = $propertyRepository;
        $this->unitRepository = $unitRepository;
        $this->partnerRepository = $partnerRepository;
    }

    public function fetchFilterOptions(IncomeDashFetchFilterOptionsRequest $request)
    {
        return response($this->composeFilterData([]));
        // This used to return a mutually exclusive list, aka, if you select a client first, it won't return
        // portfolios of other clients
        // Disabled for now by not passing in filter conditions and just returns everything
        //return response($this->composeFilterData($request->validated()));
    }

    public function fetchFilterAuxiliaryOptions()
    {
        return response([
            'property_statuses' => $this->propertyStatusRepository->getPropertyStatuses(),
            'property_uses' => $this->propertyUseRepository->getPropertyUses(),
            'property_tenures' => $this->propertyTenureRepository->getTenures(),
            'property_categories' => $this->propertyCategoryRepository->getPropertyCategories(),
            'location_types' => $this->locationTypeRepository->getLocationTypes(),
        ]);
    }

    public function fetchTimeBounds(IncomeDashFetchFilterOptionsRequest $request)
    {
        $valid_data = $request->validated();

        return response($this->leaseRepository->getIncomeFilteredOldestNewestLease(
            $valid_data['leaseType'],
            $valid_data,
            $request['selectedBarTab']
        ));
    }

    public function fetchDashData(IncomeDashFetchFilterOptionsRequest $request)
    {
        $response = [];
        $valid_data = $request->validated();

        $response = $this->leaseRepository->getIncomeFilteredLeaseData(
            $valid_data['min_year'] ?? 0,
            $valid_data['max_year'] ?? 0,
            $valid_data['leaseType'],
            $valid_data
        );

        return response($response);
    }


    public function fetchDashAuxData(IncomeDashFetchFilterOptionsRequest $request)
    {
        $response = [];
        $valid_data = $request->validated();
        $real_unit_filters = $valid_data;
        $real_unit_filters['is_virtual'] = false;
        $real_unit_filters['property_live'] = true;

        $resolvePortfolios = $this->portfolioRepository->findPortfoliosByValidData($valid_data);
        $resolveProperties = $this->propertyRepository->findPropertiesByValidData($valid_data);

        $response['client_accounts'] = $this->clientAccountRepository->identifyClientAccounts($valid_data);
        $response['portfolios'] = DashboardPortfolioResource::collection($resolvePortfolios);
        $response['properties'] = DashboardPropertyResource::collection($resolveProperties);
        $response['property_divisions'] = $this->composePropertyStats($resolveProperties);
        $response['units'] = $this->unitRepository->identifyUnits($real_unit_filters);
        $response['units_processed'] = $this->composeUnitStats($response['units']);
        $response['property_managers'] = collect([]);

        foreach ($resolveProperties as $property) {
            if (isset($property->property_manager_id)) {
                if (!$response['property_managers']->contains($property->property_manager_id)) {
                    $response['property_managers']->push($property->property_manager_id);
                }
            }
        }

        $real_unit_filters['is_virtual'] = false;

        $total_income = $this->leaseRepository->getSumValuesByValidData($real_unit_filters)->toArray();

        $response['lease_payables'] = $this->leaseRepository->identify($real_unit_filters, true);
        $response['lease_receivable_total_income'] = [
            'annual_rent' => '0.00',
            'annual_service_charge' => '0.00',
            'annual_insurance_charge' => '0.00',
            'annual_rate_charge' => '0.00',
        ];
        $response['lease_payable_total_income'] = [
            'annual_rent' => '0.00',
            'annual_service_charge' => '0.00',
            'annual_insurance_charge' => '0.00',
            'annual_rate_charge' => '0.00',
        ];

        if ($total_income) {
            foreach ($total_income as $income) {
                if ($income['payable']) {
                    $response['lease_payable_total_income'] = [
                        'annual_rent' => $income['annual_rent_sum'] ? $income['annual_rent_sum'] : '0.00',
                        'annual_service_charge' => $income['annual_service_charge_sum'] ? $income['annual_service_charge_sum'] : '0.00',
                        'annual_insurance_charge' => $income['annual_insurance_charge_sum'] ? $income['annual_insurance_charge_sum'] : '0.00',
                        'annual_rate_charge' => $income['annual_rate_charge_sum'] ? $income['annual_rate_charge_sum'] : '0.00',
                    ];
                } else {
                    $response['lease_receivable_total_income'] = [
                        'annual_rent' => $income['annual_rent_sum'] ? $income['annual_rent_sum'] : '0.00',
                        'annual_service_charge' => $income['annual_service_charge_sum'] ? $income['annual_service_charge_sum'] : '0.00',
                        'annual_insurance_charge' => $income['annual_insurance_charge_sum'] ? $income['annual_insurance_charge_sum'] : '0.00',
                        'annual_rate_charge' => $income['annual_rate_charge_sum'] ? $income['annual_rate_charge_sum'] : '0.00',
                    ];
                }
            }
        }

        $partners = $this->partnerRepository->getPartners();
        $response['partners'] = $partners->pluck('id');

        return response($response);
    }

    private function composePropertyStats($properties): array
    {
        $propertyDivisionData = [
            'by_category' => array_fill_keys(
                $this->propertyCategoryRepository->getPropertyCategories()->pluck('name')->toArray(),
                0
            ) + ['unspecified' => 0],
            'by_tenure' => array_fill_keys(
                $this->propertyTenureRepository->getTenures()->pluck('name')->toArray(),
                0
            ) + ['unspecified' => 0],
            'by_use' => array_fill_keys(
                $this->propertyUseRepository->getPropertyUses()->pluck('name')->toArray(),
                0
            ) + ['unspecified' => 0],
        ];

        foreach ($properties as $property) {
            $tmpPropCategory = $property->propertyCategory ? $property->propertyCategory->name : 'unspecified';
            $tmpPropTenure = $property->propertyTenure ? $property->propertyTenure->name : 'unspecified';
            $tmpPropUse = $property->propertyUse ? $property->propertyUse->name : 'unspecified';
            $propertyDivisionData['by_category'][$tmpPropCategory]++;
            $propertyDivisionData['by_tenure'][$tmpPropTenure]++;
            $propertyDivisionData['by_use'][$tmpPropUse]++;
        }

        $processedPropertyDivisionData = ['by_category' => [], 'by_tenure' => [], 'by_use' => []];
        foreach ($propertyDivisionData['by_category'] as $key => $val) {
            if ($val) {
                $processedPropertyDivisionData['by_category'][] = ['name' => $key, 'value' => $val];
            }
        }
        foreach ($propertyDivisionData['by_tenure'] as $key => $val) {
            if ($val) {
                $processedPropertyDivisionData['by_tenure'][] = ['name' => $key, 'value' => $val];
            }
        }
        foreach ($propertyDivisionData['by_use'] as $key => $val) {
            if ($val) {
                $processedPropertyDivisionData['by_use'][] = ['name' => $key, 'value' => $val];
            }
        }

        return $processedPropertyDivisionData;
    }

    private function composeUnitStats($units): array
    {
        $voidUnits = (new Unit())->newQueryWithoutRelationships()
            ->select(Unit::getTableName() . '.id')
            ->whereIn(Unit::getTableName() . '.id', $units)
            ->where(Unit::getTableName() . '.is_virtual', false)
            ->doesntHave('tenants')->get();

        $usedUnits = (new Unit())->newQueryWithoutRelationships()
            ->select(Unit::getTableName() . '.id')
            ->whereIn(Unit::getTableName() . '.id', $units)
            ->where(Unit::getTableName() . '.is_virtual', false)
            ->has('tenants')->get();

        return [
            'units_void' => [
                'count' => $voidUnits->count(),
                'list' => $voidUnits->pluck('id')
            ],
            'units_used' => [
                'count' => $usedUnits->count(),
                'list' => $usedUnits->pluck('id'),
                'income' => [
                    'annual_rent' => $usedUnits->sum('rentPerAnnum'),
                    'annual_service_charge' => $usedUnits->sum('serviceChargePerAnnum')
                ]
            ]
        ];
    }

    private function composeFilterData(array $validated_data)
    {
        $response = [];

        $client_account_id = $validated_data['client_account_id'] ?? 0;
        $portfolio_id = $validated_data['portfolio_id'] ?? 0;
        $property_manager_id = $validated_data['property_manager_id'] ?? 0;

        /**
         * !! I'm sure there's a cleaner way to achieve the end result here
         *  But I doubt there's a much more efficient way. Readability goes out the window where efficiency is important
         *  some conditions could be grouped, we'll handle each case separately to avoid nesting and making this
         *  impossible to understand later on
         *
         *    Hate to break it to you Aron but it was never hitting FTT or FTF because you weren't always checking all 3
         *    Also there was a cleaner way so I've refactored this
         *
         * PREPARE FOR 8 IF STATEMENTS FOR ALL ISSET() PERMUTATIONS ON [CLIENT, PORTFOLIO, MANAGER]
         *         TTT TTF TFT
         *         TFF FTT FTF
         *         FFT FFF (in this order)
         */
        $type = '';
        $type .= $client_account_id ? 'T' : 'F';
        $type .= $portfolio_id ? 'T' : 'F';
        $type .= $property_manager_id ? 'T' : 'F';

        switch ($type) {
            case 'TTT': // client, portfolio, manager
            case 'FTT': // portfolio, manager
                $tmpPortfolio = $this->portfolioRepository->getPortfolio($portfolio_id);

                $response['portfolios'] = collect([$tmpPortfolio]);
                $response['client_accounts'] = collect([$tmpPortfolio->clientAccount]);
                $response['property_managers'] = collect([
                    $this->propertyManagerRepository->getPropertyManager($property_manager_id)
                ]);
                break;
            case 'TTF': // client, portfolio
            case 'FTF': // portfolio
                $tmpPortfolio = $this->portfolioRepository->getPortfolio($portfolio_id);
                $tmpPropertyManagerCollection = [$tmpPortfolio->clientAccount->propertyManager];

                foreach ($tmpPortfolio->properties as $property) {
                    if (!$tmpPropertyManagerCollection->contains('id', $property->propertyManager->id)) {
                        $tmpPropertyManagerCollection[] = $property->propertyManager;
                    }

                    foreach ($property->units as $unit) {
                        if (!$tmpPropertyManagerCollection->contains('id', $unit->propertyManager->id)) {
                            $tmpPropertyManagerCollection[] = $unit->propertyManager;
                        }
                    }
                }

                $response['portfolios'] = collect([$tmpPortfolio]);
                $response['client_accounts'] = collect([$tmpPortfolio->clientAccount]);
                $response['property_managers'] = collect($tmpPropertyManagerCollection);
                break;
            case 'TFT': // client, manager
                $tmpClientAccount = $this->clientAccountRepository->getClientAccount($client_account_id);
                $tmpPropertyManager = $this->propertyManagerRepository->getPropertyManager($property_manager_id);
                $tmpPortfolioCollection = collect([]);

                foreach ($tmpClientAccount->portfolios as $portfolio) {
                    // At this stage there is no need to check for duplicates
                    if ($portfolio->clientAccount->property_manager_id === $tmpPropertyManager->id) {
                        $tmpPortfolioCollection->push($portfolio);
                    }
                    foreach ($portfolio->properties as $property) {
                        if (
                            $property->property_manager_id === $tmpPropertyManager->id &&
                            !$tmpPortfolioCollection->contains('id', $property->portfolio_id)
                        ) {
                            $tmpPortfolioCollection->push(
                                $this->portfolioRepository->getPortfolio($property->portfolio_id)
                            );
                        }
                        foreach ($property->units as $unit) {
                            if (
                                $unit->property_manager_id === $tmpPropertyManager->id &&
                                !$tmpPortfolioCollection->contains('id', $unit->property->portfolio_id)
                            ) {
                                $tmpPortfolioCollection->push(
                                    $this->portfolioRepository->getPortfolio($unit->property->portfolio_id)
                                );
                            }
                        }
                    }
                }

                $response['portfolios'] = $tmpPortfolioCollection;
                $response['client_accounts'] = collect([$tmpClientAccount]);
                $response['property_managers'] = collect([$tmpPropertyManager]);
                break;
            case 'TFF': // client
                $tmpClientAccount = $this->clientAccountRepository->getClientAccount($client_account_id);
                $tmpPortfolioCollection = $tmpClientAccount->portfolios;
                $tmpPropertyManagerCollection = [$tmpClientAccount->propertyManager];

                foreach ($tmpPortfolioCollection as $portfolio) {
                    foreach ($portfolio->properties as $property) {
                        if (!$tmpPropertyManagerCollection->contains('id', $property->property_manager_id)) {
                            $tmpPropertyManagerCollection[] =
                                $this->propertyManagerRepository->getPropertyManager($property->property_manager_id);

                            foreach ($property->units as $unit) {
                                if (!$tmpPropertyManagerCollection->contains('id', $unit->property_manager_id)) {
                                    $tmpPropertyManagerCollection[] = $this->propertyManagerRepository
                                        ->getPropertyManager(
                                            $unit->property_manager_id
                                        );
                                }
                            }
                        }
                    }
                }

                $response['portfolios'] = $tmpPortfolioCollection;
                $response['client_accounts'] = collect([$tmpClientAccount]);
                $response['property_managers'] = collect($tmpPropertyManagerCollection);
                break;
            case 'FFT': // manager
                $tmpClientAccountCollection = [];
                $tmpPortfolioCollection = [];
                $tmpPropertyManager = $this->propertyManagerRepository->getPropertyManager(
                    $property_manager_id
                );

                foreach ($tmpPropertyManager->clientAccounts as $clientAccount) {
                    if (!$tmpClientAccountCollection->contains('id', $clientAccount->id)) {
                        $tmpClientAccountCollection[] = $clientAccount;
                    }

                    // Same portfolio won't belong to two client accounts, no need to waste time checking duplicates
                    $tmpPortfolioCollection->merge($clientAccount->portfolios);
                }

                foreach ($tmpPropertyManager->properties as $property) {
                    if (!$tmpPortfolioCollection->contains('id', $property->portfolio_id)) {
                        $tmpPortfolioCollection[] =
                            $this->portfolioRepository->getPortfolio($property->portfolio_id);
                    }
                }

                foreach ($tmpPropertyManager->units as $unit) {
                    if (!$tmpPortfolioCollection->contains('id', $unit->property->portfolio_id)) {
                        $tmpPortfolioCollection[] =
                            $this->portfolioRepository->getPortfolio($unit->property->portfolio_id);
                    }
                }

                $response['portfolios'] = collect($tmpPortfolioCollection);
                $response['client_accounts'] = collect($tmpClientAccountCollection);
                $response['property_managers'] = collect([$tmpPropertyManager]);
                break;
            case 'FFF': // NADA
                $response['portfolios'] = $this->portfolioRepository->getPortfolios();
                $response['client_accounts'] = $this->clientAccountRepository->getClientAccounts(0, 0, 0);
                $response['property_managers'] = $this->propertyManagerRepository->getPropertyManagers();
                break;
        }

        /**
         * Add the auxiliaries regardless of filter state
         */
        if (array_key_exists('property_status_id', $validated_data)) {
            $response['property_statuses'] = $this->propertyStatusRepository->getPropertyStatuses();
        }
        if (array_key_exists('property_use_id', $validated_data)) {
            $response['property_uses'] = $this->propertyUseRepository->getPropertyUses();
        }
        if (array_key_exists('property_tenure_id', $validated_data)) {
            $response['property_tenures'] = $this->propertyTenureRepository->getTenures();
        }
        if (array_key_exists('property_category_id', $validated_data)) {
            $response['property_categories'] = $this->propertyCategoryRepository->getPropertyCategories();
        }
        if (array_key_exists('location_type_id', $validated_data)) {
            $response['location_types'] = $this->locationTypeRepository->getLocationTypes();
        }

        return $response;
    }
}
