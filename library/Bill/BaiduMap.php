<?php

class Bill_BaiduMap
{
    private static $baiduUrlApiLocationPrefix = 'http://api.map.baidu.com/geocoder/v2/?ak=DD55be08e3404eca6ff7320129d13869&output=json&address=';
    private static $baiduUrlApiLnglatPrefix = 'http://api.map.baidu.com/geocoder/v2/?ak=DD55be08e3404eca6ff7320129d13869&output=json&location=';

    public static function getLngLatByAddress($address)
    {
        $addressContent = self::_getBaiduAddressContent($address);

        $lngLatInfo = array();
        if ($addressContent['status'] == 0) {
            $lngLatInfo['Longitude'] = $addressContent['result']['location']['lng'];
            $lngLatInfo['Latitude'] = $addressContent['result']['location']['lat'];
        }

        return $lngLatInfo;
    }

    public static function getCityProvinceByLngLat($lat, $lng)
    {
        $coordinate = $lat . ',' . $lng;

        $lnglatContent = file_get_contents(self::$baiduUrlApiLnglatPrefix . $coordinate);
        $decodeContent = json_decode($lnglatContent, true);

        $cityProvinceInfo = array();
        if ($decodeContent['status'] == 0) {
            $cityProvinceInfo['City'] = $decodeContent['result']['addressComponent']['city'];
            $cityProvinceInfo['Province'] = $decodeContent['result']['addressComponent']['province'];
        }

        return $cityProvinceInfo;
    }

    public static function getAddressInfo($address)
    {
        $addressInfo = self::getLngLatByAddress($address);

        if (!empty($addressInfo)) {
            $cityProvinceInfo = self::getCityProvinceByLngLat($addressInfo['Latitude'], $addressInfo['Longitude']);

            $addressInfo['City'] = $cityProvinceInfo['City'];
            $addressInfo['Province'] = $cityProvinceInfo['Province'];
        }

        return $addressInfo;
    }

    private static function _getBaiduAddressContent($address)
    {
        $url = self::$baiduUrlApiLocationPrefix . urlencode(str_replace(array("\n", "\r", "\r\n", "\t", ' '), '', $address));
        $addressContent = file_get_contents($url);
        $decodeContent = json_decode($addressContent, true);

        return $decodeContent;
    }
}