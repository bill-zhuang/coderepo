<?php

class Bill_Network
{
	//reference url: http://stackoverflow.com/questions/858883/run-php-task-asynchronously
	//add ignore_user_abort(true); set_time_limit(0); before script start.
	//$host = $_SERVER['HTTP_HOST'];
	public static function callingScript($script_url, $host)
	{
		$socket_con = fsockopen($host, 80, $errno, $errstr, 10);

		if($socket_con)
		{
			//$script_url=$remote_house/script.php?parameters=...
			$socket_data = "GET $script_url HTTP 1.1\r\nHost: $host\r\nConnection: Close\r\n\r\n";
			fwrite($socket_con, $socket_data);
			fclose($socket_con);
		}
	}


}