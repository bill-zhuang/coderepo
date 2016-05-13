<?php

class Bill_Curl
{
    public static function getResponseHeaders($requestUrl)
    {
        $ch = curl_init($requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);

        $response = curl_exec($ch);
        return curl_getinfo($ch);
    }

    public static function sendRequestByCurl($requestUrl, array $data, $method = Bill_Constant::HTTP_METHOD_POST)
    {
        $ch = curl_init();
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (strtoupper($method) == Bill_Constant::HTTP_METHOD_GET) {
            curl_setopt($ch, CURLOPT_URL, $requestUrl . '?' . http_build_query($data));
        } else {
            curl_setopt($ch, CURLOPT_URL, $requestUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        return $result;
    }
}