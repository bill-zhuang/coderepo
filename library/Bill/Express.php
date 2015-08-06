<?php

class Bill_Express
{
    public function getExpressInfo($express_code)
    {
        $request_url = 'http://www.kuaidi100.com/query';
        $method = 'GET';
        $express_company = $this->_getExpressCompanyName($express_code);
        if ($express_company !== false)
        {
            $param = array('type' => $express_company, 'postid' => $express_code);
            $json_data = Bill_Curl::sendRequestByCurl($request_url, $param, $method);
            $decode_data = json_decode($json_data, true);
            if ($decode_data['message'] == 'ok')
            {
                return $decode_data['data'];
            }
        }

        return false;
    }

    private function _getExpressCompanyName($express_code)
    {
        $request_url = 'http://www.kuaidi100.com/autonumber/auto';
        $param = array('num' => $express_code);
        $method = 'GET';

        $json_data = Bill_Curl::sendRequestByCurl($request_url, $param, $method);
        $decode_data = json_decode($json_data, true);
        if (!empty($decode_data))
        {
            return $decode_data[0]['comCode'];
        }

        return false;
    }
} 