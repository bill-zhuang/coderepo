<?php

class Bill_Network
{
    //reference url: http://stackoverflow.com/questions/858883/run-php-task-asynchronously
    //add ignore_user_abort(true); set_time_limit(0); before script start.
    //$host = $_SERVER['HTTP_HOST'];
    public static function callingScript($scriptUrl, $host)
    {
        $socketCon = fsockopen($host, 80, $errorNo, $errorStr, 10);

        if ($socketCon) {
            //$scriptUrl=$remoteHouse/script.php?parameters=...
            $socketData = "GET $scriptUrl HTTP/1.1\r\n";
            $socketData .= "Host: $host\r\n";
            $socketData .= "Cookie: PHPSESSID=" . $_COOKIE['PHPSESSID'] . "\r\n";
            $socketData .= "Connection: Close\r\n\r\n";
            fwrite($socketCon, $socketData);
            /*while (!feof($socketCon))
            {
                echo fgets($socketCon, 128);
            }*/
            fclose($socketCon);
        } else {
            echo "$errorStr ($errorNo)<br />\n";
        }
    }
}