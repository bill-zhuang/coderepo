<?php

class Bill_Network
{
    //reference url: http://stackoverflow.com/questions/858883/run-php-task-asynchronously
    //add ignore_user_abort(true); set_time_limit(0); before script start.
    //$host = $_SERVER['HTTP_HOST'];
    public static function callingScript($script_url, $host)
    {
        $socket_con = fsockopen($host, 80, $error_no, $error_str, 10);

        if($socket_con)
        {
            //$script_url=$remote_house/script.php?parameters=...
            $socket_data = "GET $script_url HTTP/1.1\r\n";
            $socket_data .= "Host: $host\r\n";
            $socket_data .= "Cookie: PHPSESSID=" . $_COOKIE['PHPSESSID'] . "\r\n";
            $socket_data .= "Connection: Close\r\n\r\n";
            fwrite($socket_con, $socket_data);
            /*while (!feof($socket_con))
            {
                echo fgets($socket_con, 128);
            }*/
            fclose($socket_con);
        }
        else
        {
            echo "$error_str ($error_no)<br />\n";
        }
    }


}