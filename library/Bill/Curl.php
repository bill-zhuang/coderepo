<?php

class Bill_Curl
{
    public static function getResponseHeaders($request_url)
    {
        $ch = curl_init($request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);

        $response = curl_exec($ch);
        return curl_getinfo($ch);
    }

    public static function sendRequestByCurl($request_url, array $data, $method = 'POST')
    {
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (strtolower($method) == 'get') {
            curl_setopt($ch, CURLOPT_URL, $request_url . '?' . http_build_query($data));
        } else {
            curl_setopt($ch, CURLOPT_URL, $request_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        return $result;
    }
}