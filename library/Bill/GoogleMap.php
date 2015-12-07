<?php

class Bill_GoogleMap
{
    private static $google_url_api_location_prefix = 'http://maps.googleapis.com/maps/api/geocode/json?sensor=false&language=zh_CN&address=';
    private static $google_url_api_direction_prefix = 'http://maps.googleapis.com/maps/api/directions/json?';

    public static function getLngLatByAddress($address)
    {
        $address_content = self::_getAddressContents($address);

        $lng_lat_array = array();
        if ($address_content['status'] == 'OK') {
            $lng_lat_array['Longitude'] = $address_content['results'][0]['geometry']['location']['lng'];
            $lng_lat_array['Latitude'] = $address_content['results'][0]['geometry']['location']['lat'];
        }

        return $lng_lat_array;
    }

    public static function getCityProvinceByAddress($address)
    {
        $address_content = self::_getAddressContents($address);

        $city_province_array = array();
        if ($address_content['status'] == 'OK') {
            foreach ($address_content['results'][0]['address_components'] as $address_componet) {
                if ($address_componet['types'][0] == 'locality') {
                    $city_province_array['City'] = $address_componet['long_name'];
                }

                if ($address_componet['types'][0] == 'administrative_area_level_1') {
                    $city_province_array['Province'] = $address_componet['long_name'];
                }
            }
        }

        return $city_province_array;
    }

    public static function getPathDistanceByCoordinates($source_lat, $source_lng, $dest_lat, $dest_lng)
    {
        $http_query = "origin={$source_lat},{$source_lng}&destination={$dest_lat},{$dest_lng}&sensor=false";
        $content = file_get_contents(self::$google_url_api_direction_prefix . $http_query);
        $decode_content = json_decode($content, true);

        if ($decode_content['status'] == 'OK') {
            return ($decode_content['routes'][0]['legs'][0]['distance']['value'] / 1000);
        } else {
            return 'get distance failed';
        }
    }

    public static function getStraigntLineDistanceByCoordinate($source_lat, $source_lng, $dest_lat, $dest_lng)
    {
        $theta = $source_lng - $dest_lng;
        $dist = sin(deg2rad($source_lat)) * sin(deg2rad($dest_lat)) + cos(deg2rad($source_lat)) * cos(deg2rad($dest_lat)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return ($miles * 1.609344);
    }

    private static function _getAddressContents($address)
    {
        $address_nospace = str_replace(array("\n", "\r", "\r\n", "\t", ' '), '', $address);
        $address_content = file_get_contents(self::$google_url_api_location_prefix . $address_nospace);

        return json_decode($address_content, true);
    }
}