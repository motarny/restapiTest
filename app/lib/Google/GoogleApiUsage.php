<?php

namespace Lib\Google;


use Lib\Locations\Location;

class GoogleApiUsage
{

    const API_KEY = 'AIzaSyASUM8ZSuF_irk8MyuZqJaH1rt1mFufKGE';


    /**
     * Pobiera informacje o lokalizacji - długość i szerokość geograficzną
     * @param Location $locationObj
     *
     * @return array|false
     */
    static public function getLocationInfo(Location $locationObj)
    {
        $address = $locationObj->getAddress();
        if (!$address) {
            return false;
        }

        $urlRequest = 'https://maps.googleapis.com/maps/api/geocode/json?address=';
        $urlRequest .= urlencode($address);
        $urlRequest .= '&key=' . GoogleApiUsage::API_KEY;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlRequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $data = curl_exec($ch);
        curl_close($ch);

        $jsonResponse = json_decode($data, true);

        if ($jsonResponse['status'] == 'OK') {
            $geometry = $jsonResponse['results'][0]['geometry']['location'];

            return $geometry;
        }

        return false;
    }


    /**
     * Pobiera odległość między dwiema lokalizacjami
     *
     * @param Location $locationObj
     *
     * @return int|false
     */
    static public function getDistance(Location $location1, Location $location2)
    {
        $loc1Latitude = $location1->getLatitude();
        $loc1Longitude = $location1->getLongitude();

        $loc2Latitude = $location2->getLatitude();
        $loc2Longitude = $location2->getLongitude();

        $urlRequest = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&mode=walking&origins=';
        $urlRequest .= $loc1Latitude . ',' . $loc1Longitude . '&destinations=' . $loc2Latitude . ',' . $loc2Longitude;
        $urlRequest .= '&key=' . GoogleApiUsage::API_KEY;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlRequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $data = curl_exec($ch);
        curl_close($ch);

        $jsonResponse = json_decode($data, true);
        if ($jsonResponse['status'] == 'OK') {
            $distance = $jsonResponse['rows'][0]['elements']['0']['distance']['value'];

            return $distance;
        }

        return false;

    }

}