<?php 

class Bill_Curl
{
    private $_url;
    
    public function __construct($url)
    {
        $this->_url = $url;
    }
    
    public function getResponseHeaders()
    {
        $ch = curl_init($this->_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        
        $response = curl_exec($ch);
        return curl_getinfo($ch);
    }
}