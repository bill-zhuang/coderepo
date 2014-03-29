<?php

class Bill_ConvertCoordinate
{
	private static $baidu_url_api_convert_prefix = 'http://api.map.baidu.com/ag/coord/convert?';

	//如需简化,谷歌得到的经纬度经过如下计算近似即可得到百度的lng + 0.0065 lat + 0.0060
	//x/y：经纬度坐标from/to：决定转换效果，具体参数填充如下：from=2表示谷歌from=0表示gpsto=4 表示百度
	public static function fromGoogleToBaiduByApi($lng, $lat)
	{
		$http_query = "from=2&to=4&x={$lng}&y={$lat}";
		$content = file_get_contents(self::$baidu_url_api_convert_prefix . $http_query);
		$decode_content = json_decode($content, true);

		return $decode_content;
	}

	//all below url reference: https://on4wp7.codeplex.com/SourceControl/changeset/view/21483#353936
	public static function fromGCJToBD($gcj_lat, $gcj_lng)
	{
		$lat_pi = M_PI * 3000.0 / 180.0;

		$z = sqrt($gcj_lat * $gcj_lat + $gcj_lng * $gcj_lng) + 0.00002 * sin($gcj_lat * $lat_pi);
		$theta = atan2($gcj_lat, $gcj_lng) - 0.000003 * cos($gcj_lng * $lat_pi);

		$bd_lng = $z * cos($theta) + 0.0065;
		$bd_lat = $z * sin($theta) + 0.006;

		return array('Longitude' => $bd_lng, 'Latitude' => $bd_lat);
	}

	public static function fromBDToGCJ($db_lat, $bd_lng)
	{
		$lat_pi = M_PI * 3000.0 / 180.0;

		$lng = $bd_lng - 0.0065;
		$lat = $db_lat - 0.006;

		$z = sqrt($lat * $lat + $lng * $lng) - 0.00002 * sin($lat * $lat_pi);
		$theta = atan2($lat, $lng) - 0.000003 * cos($lng * $lat_pi);

		$gcj_lng = $z * cos($theta);
		$gcj_lat = $z * sin($theta);

		return array('Longitude' => $gcj_lng, 'Latitude' => $gcj_lat);
	}

	public static function fromWGSToGCJ($wgs_lat, $wgs_lng)
	{
		$a = 6378245.0;
		$ee = 0.00669342162296594323;

		$d_lat = self::wgsLat($wgs_lng - 105.0, $wgs_lat - 35.0);
		$d_lng = self::wgsLng($wgs_lng - 105.0, $wgs_lat - 35.0);

		$rad_lat = $wgs_lat / 180.0 * M_PI;
		$magic_lat = sin($rad_lat);
		$magic_lat = 1- $ee * $magic_lat * $magic_lat;
		$magic_lat_sqrt = sqrt($magic_lat);

		$d_lat = ($d_lat * 180.0) / (($a * (1 - $ee))) / (($magic_lat * $magic_lat_sqrt) * M_PI);
		$d_lng = ($d_lng * 180.0) / ($a / $magic_lat_sqrt * cos($rad_lat) * M_PI);
		 
		return array('Longitude' => $wgs_lng + $d_lng, 'Latitude' => $wgs_lat + $wgs_lat);
	}

	private static function wgsLat($wgs_lng, $wgs_lat)
	{
		$ret = -100.0 + 2.0 * $wgs_lng + 3.0 * $wgs_lat + 0.2 * $wgs_lat * $wgs_lat 
		    + 0.1 * $wgs_lng * $wgs_lat + 0.2 * sqrt(abs($wgs_lng));
		$ret += (20.0 * sin(6.0 * $wgs_lng * M_PI) + 20.0 * sin(2.0 * $wgs_lng * M_PI)) * 2.0 / 3.0;
		$ret += (20.0 * sin($wgs_lat * M_PI) + 40.0 * sin($wgs_lat / 3.0 * M_PI)) * 2.0 / 3.0;
		$ret += (160.0 * sin($wgs_lat / 12.0 * M_PI) + 320 * sin($wgs_lat * M_PI / 30.0)) * 2.0 / 3.0;

		return $ret;
	}

	private static function wgsLng($wgs_lng, $wgs_lat)
	{
		$ret = 300.0 + $wgs_lng + 2.0 * $wgs_lat + 0.1 * $wgs_lng * $wgs_lng 
		    + 0.1 * $wgs_lng * $wgs_lat + 0.1 * sqrt(abs($wgs_lng));
		$ret += (20.0 * sin(6.0 * $wgs_lng * M_PI) + 20.0 * sin(2.0 * $wgs_lng * M_PI)) * 2.0 / 3.0;
		$ret += (20.0 * sin($wgs_lng * M_PI) + 40.0 * sin($wgs_lng / 3.0 * M_PI)) * 2.0 / 3.0;
		$ret += (150.0 * sin($wgs_lng / 12.0 * M_PI) + 300.0 * sin($wgs_lng / 30.0 * M_PI)) * 2.0 / 3.0;

		return $ret;
	}
}