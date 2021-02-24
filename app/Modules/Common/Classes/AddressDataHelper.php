<?php

namespace App\Modules\Common\Classes;

use App\Modules\Core\Library\MapHelper;

class AddressDataHelper
{
    /**
     * @param array $data
     */
    public static function setGpsData(array &$data)
    {
        if (!empty($data['postcode'])) {
            $gps_data = MapHelper::geocodePostcode($data['postcode']);

            $data['latitude']  = $gps_data['lat'];
            $data['longitude'] = $gps_data['long'];
        }
    }

    public static function getGpsData($postcode)
    {
        $data = [];
        $gps_data = MapHelper::geocodePostcode($postcode);

        $data['latitude']  = $gps_data['lat'];
        $data['longitude'] = $gps_data['long'];

        return $data;
    }
}
