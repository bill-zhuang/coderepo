<?php

class Bill_ConvertCoordinate
{
    private static $baiduUrlApiConvertPrefix = 'http://api.map.baidu.com/ag/coord/convert?';

    //如需简化,谷歌得到的经纬度经过如下计算近似即可得到百度的lng + 0.0065 lat + 0.0060
    //x/y：经纬度坐标from/to：决定转换效果，具体参数填充如下：from=2表示谷歌from=0表示gpsto=4 表示百度
    public static function fromGoogleToBaiduByApi($lng, $lat)
    {
        $httpQuery = "from=2&to=4&x={$lng}&y={$lat}";
        $content = file_get_contents(self::$baiduUrlApiConvertPrefix . $httpQuery);
        $decodeContent = json_decode($content, true);

        return $decodeContent;
    }

    //all below url reference: https://on4wp7.codeplex.com/SourceControl/changeset/view/21483#353936
    public static function fromGCJToBD($gcjLat, $gcjLng)
    {
        $latPi = M_PI * 3000.0 / 180.0;

        $z = sqrt($gcjLat * $gcjLat + $gcjLng * $gcjLng) + 0.00002 * sin($gcjLat * $latPi);
        $theta = atan2($gcjLat, $gcjLng) - 0.000003 * cos($gcjLng * $latPi);

        $bdLng = $z * cos($theta) + 0.0065;
        $bdLat = $z * sin($theta) + 0.006;

        return array('Longitude' => $bdLng, 'Latitude' => $bdLat);
    }

    public static function fromBDToGCJ($dbLat, $bdLng)
    {
        $latPi = M_PI * 3000.0 / 180.0;

        $lng = $bdLng - 0.0065;
        $lat = $dbLat - 0.006;

        $z = sqrt($lat * $lat + $lng * $lng) - 0.00002 * sin($lat * $latPi);
        $theta = atan2($lat, $lng) - 0.000003 * cos($lng * $latPi);

        $gcjLng = $z * cos($theta);
        $gcjLat = $z * sin($theta);

        return array('Longitude' => $gcjLng, 'Latitude' => $gcjLat);
    }

    public static function fromWGSToGCJ($wgsLat, $wgsLng)
    {
        $a = 6378245.0;
        $ee = 0.00669342162296594323;

        $dLat = self::wgsLat($wgsLng - 105.0, $wgsLat - 35.0);
        $dLng = self::wgsLng($wgsLng - 105.0, $wgsLat - 35.0);

        $radLat = $wgsLat / 180.0 * M_PI;
        $magicLat = sin($radLat);
        $magicLat = 1 - $ee * $magicLat * $magicLat;
        $magicLat_sqrt = sqrt($magicLat);

        $dLat = ($dLat * 180.0) / (($a * (1 - $ee))) / (($magicLat * $magicLat_sqrt) * M_PI);
        $dLng = ($dLng * 180.0) / ($a / $magicLat_sqrt * cos($radLat) * M_PI);

        return array('Longitude' => $wgsLng + $dLng, 'Latitude' => $wgsLat + $wgsLat);
    }

    private static function wgsLat($wgsLng, $wgsLat)
    {
        $ret = -100.0 + 2.0 * $wgsLng + 3.0 * $wgsLat + 0.2 * $wgsLat * $wgsLat + 0.1 * $wgsLng * $wgsLat + 0.2 * sqrt(abs($wgsLng));
        $ret += (20.0 * sin(6.0 * $wgsLng * M_PI) + 20.0 * sin(2.0 * $wgsLng * M_PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($wgsLat * M_PI) + 40.0 * sin($wgsLat / 3.0 * M_PI)) * 2.0 / 3.0;
        $ret += (160.0 * sin($wgsLat / 12.0 * M_PI) + 320 * sin($wgsLat * M_PI / 30.0)) * 2.0 / 3.0;

        return $ret;
    }

    private static function wgsLng($wgsLng, $wgsLat)
    {
        $ret = 300.0 + $wgsLng + 2.0 * $wgsLat + 0.1 * $wgsLng * $wgsLng + 0.1 * $wgsLng * $wgsLat + 0.1 * sqrt(abs($wgsLng));
        $ret += (20.0 * sin(6.0 * $wgsLng * M_PI) + 20.0 * sin(2.0 * $wgsLng * M_PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($wgsLng * M_PI) + 40.0 * sin($wgsLng / 3.0 * M_PI)) * 2.0 / 3.0;
        $ret += (150.0 * sin($wgsLng / 12.0 * M_PI) + 300.0 * sin($wgsLng / 30.0 * M_PI)) * 2.0 / 3.0;

        return $ret;
    }
}