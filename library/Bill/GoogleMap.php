<?php

class Bill_GoogleMap
{
    private static $googleUrlApiLocationPrefix = 'http://maps.googleapis.com/maps/api/geocode/json?sensor=false&language=zh_CN&address=';
    private static $googleUrlApiDirectionPrefix = 'http://maps.googleapis.com/maps/api/directions/json?';

    public static function getLngLatByAddress($address)
    {
        $addressContent = self::_getAddressContents($address);

        $lngLatArray = array();
        if ($addressContent['status'] == 'OK') {
            $lngLatArray['Longitude'] = $addressContent['results'][0]['geometry']['location']['lng'];
            $lngLatArray['Latitude'] = $addressContent['results'][0]['geometry']['location']['lat'];
        }

        return $lngLatArray;
    }

    public static function getCityProvinceByAddress($address)
    {
        $addressContent = self::_getAddressContents($address);

        $cityProvinceArray = array();
        if ($addressContent['status'] == 'OK') {
            foreach ($addressContent['results'][0]['address_components'] as $addressComponent) {
                if ($addressComponent['types'][0] == 'locality') {
                    $cityProvinceArray['City'] = $addressComponent['long_name'];
                }

                if ($addressComponent['types'][0] == 'administrative_area_level_1') {
                    $cityProvinceArray['Province'] = $addressComponent['long_name'];
                }
            }
        }

        return $cityProvinceArray;
    }

    public static function getPathDistanceByCoordinates($sourceLat, $sourceLng, $destLat, $destLng)
    {
        $httpQuery = "origin={$sourceLat},{$sourceLng}&destination={$destLat},{$destLng}&sensor=false";
        $content = file_get_contents(self::$googleUrlApiDirectionPrefix . $httpQuery);
        $decodeContent = json_decode($content, true);

        if ($decodeContent['status'] == 'OK') {
            return ($decodeContent['routes'][0]['legs'][0]['distance']['value'] / 1000);
        } else {
            return 'get distance failed';
        }
    }

    public static function getStraigntLineDistanceByCoordinate($sourceLat, $sourceLng, $destLat, $destLng)
    {
        $theta = $sourceLng - $destLng;
        $dist = sin(deg2rad($sourceLat)) * sin(deg2rad($destLat)) + cos(deg2rad($sourceLat)) * cos(deg2rad($destLat)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return ($miles * 1.609344);
    }

    private static function _getAddressContents($address)
    {
        $addressNospace = str_replace(array("\n", "\r", "\r\n", "\t", ' '), '', $address);
        $addressContent = file_get_contents(self::$googleUrlApiLocationPrefix . $addressNospace);

        return json_decode($addressContent, true);
    }
}