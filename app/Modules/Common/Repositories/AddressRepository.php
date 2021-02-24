<?php
namespace App\Modules\Common\Repositories;

use App\Modules\Common\Classes\AddressDataHelper;
use App\Modules\Common\Models\Country;
use App\Modules\Common\Models\County;
use App\Modules\Common\Models\Address;
use App\Modules\Core\Classes\Repository;
use App\Modules\Core\Interfaces\IYardiImport;
use App\Modules\Core\Library\EloquentHelper;
use App\Modules\Edits\Models\ReviewStatus;
use Illuminate\Support\Collection;

class AddressRepository extends Repository implements IYardiImport
{
    /**
     * AddressRepository constructor.
     * @param Address $model
     */
    public function __construct(Address $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getAddresses(): Collection
    {
        return $this->model->orderBy('street', 'asc')->get();
    }

    /**
     * @param $id
     * @return Address
     */
    public function getAddress($id): Address
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array $data
     * @return Address
     */
    public function storeAddress(array $data): Address
    {
        AddressDataHelper::setGpsData($data);
        $data['review_status_id'] = EloquentHelper::getRecordIdBySlug(
            ReviewStatus::class,
            ReviewStatus::NEVER_REVIEWED
        );

        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return Address
     */
    public function updateAddress(int $id, array $data): Address
    {
        $Address = $this->getAddress($id);

        if (!array_key_exists('latitude', $data) && !array_key_exists('longitude', $data)) {
            AddressDataHelper::setGpsData($data);
        }

        $Address->update($data);
        return $Address;
    }

    /**
     * @param int $id
     * @return Address
     * @throws \Exception
     */
    public function deleteAddress(int $id): Address
    {
        $Address = $this->getAddress($id);
        $Address->delete();
        return $Address;
    }

    /**
     * @param array $data
     * @return Address
     */
    public function importRecord(array $data, int $id = null)
    {
        $county  = County::where('name', trim($data['county']))->first();
        $country = Country::where('name', trim($data['country']))->first();

        $limitedData = [
            'country_id' => $country ? $country->id : null,
            'county_id'  => $county ? $county->id : null,
            'building'   => trim($data['building']),
            'number'     => trim($data['number']),
            'street'     => trim($data['street']),
            'estate'     => trim($data['estate']),
            'suburb'     => trim($data['suburb']),
            'town'       => trim($data['town']),
            'postcode'   => trim($data['postcode'])
        ];

        if ($id) {
            return $this->updateAddress($id, $limitedData);
        } else {
            return $this->storeAddress($limitedData);
        }
    }
}
