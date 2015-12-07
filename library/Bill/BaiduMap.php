<?php

class Bill_BaiduMap
{
    private static $baidu_url_api_location_prefix = 'http://api.map.baidu.com/geocoder/v2/?ak=DD55be08e3404eca6ff7320129d13869&output=json&address=';
    private static $baidu_url_api_lnglat_prefix = 'http://api.map.baidu.com/geocoder/v2/?ak=DD55be08e3404eca6ff7320129d13869&output=json&location=';

    public static function getLngLatByAddress($address)
    {
        $address_content = self::_getBaiduAddressContent($address);

        $lng_lat_info = array();
        if ($address_content['status'] == 0) {
            $lng_lat_info['Longitude'] = $address_content['result']['location']['lng'];
            $lng_lat_info['Latitude'] = $address_content['result']['location']['lat'];
        }

        return $lng_lat_info;
    }

    public static function getCityProvinceByLngLat($lat, $lng)
    {
        $coordinate = $lat . ',' . $lng;

        $lnglat_content = file_get_contents(self::$baidu_url_api_lnglat_prefix . $coordinate);
        $decode_content = json_decode($lnglat_content, true);

        $city_province_info = array();
        if ($decode_content['status'] == 0) {
            $city_province_info['City'] = $decode_content['result']['addressComponent']['city'];
            $city_province_info['Province'] = $decode_content['result']['addressComponent']['province'];
        }

        return $city_province_info;
    }

    public static function getAddressInfo($address)
    {
        $address_info = self::getLngLatByAddress($address);

        if (!empty($address_info)) {
            $city_province_info = self::getCityProvinceByLngLat($address_info['Latitude'], $address_info['Longitude']);

            $address_info['City'] = $city_province_info['City'];
            $address_info['Province'] = $city_province_info['Province'];
        }

        return $address_info;
    }

    private static function _getBaiduAddressContent($address)
    {
        $url = self::$baidu_url_api_location_prefix . urlencode(str_replace(array("\n", "\r", "\r\n", "\t", ' '), '', $address));
        $address_content = file_get_contents($url);
        $decode_content = json_decode($address_content, true);

        return $decode_content;
    }
}