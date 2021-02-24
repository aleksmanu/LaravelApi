<?php

namespace App\Modules\Core\Library;

class MapHelper
{

    /**
     * @param $postcode
     * @return array
     */
    public static function geocodePostcode($postcode)
    {

        $result = app('geocoder')->geocode($postcode)->get()->toArray();

        if ($result && $result != null) {
            $data = $result[0]->toArray();

            if ($data['latitude'] != null) {
                $lat = $data['latitude'];
            } else {
                $lat = null;
            }

            if ($data['longitude'] != null) {
                $long = $data['longitude'];
            } else {
                $lat = null;
            }

            return [
                'lat'  => $lat,
                'long' => $long
            ];
        } else {
            return [
                'lat'  => null,
                'long' => null
            ];
        }
    }
}
