<?php

class GoogleMapController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->setLayout('layout');
    }

    public function indexAction()
    {
        // action body
    }
    
    public function markLocationAction()
    {
        $json_array = [];
        $params = $this->_getParam('params', []);
        if(isset($params['location'])) {
            $coordinate_info = Bill_GoogleMap::getLngLatByAddress($params['location']);
            if (!empty($coordinate_info)) {
                $json_array['data'] = $coordinate_info;
            } else {
                $json_array['error'] = Bill_Util::getJsonResponseErrorArray('200', 'Fetch Location coordinate failed.');
            }
        } else {
            $json_array['error'] = Bill_Util::getJsonResponseErrorArray('200', 'Param location not provided.');
        }
        
        echo json_encode($json_array);
        exit;
    }

    public function multipleLocationAction()
    {

    }

    public function ajaxMultipleLocationAction()
    {
        $json_array = [
            'data' => [
                'coordinates' => $this->_multipleLocation()
            ],
        ];
        echo json_encode($json_array);
        exit;
    }

    private function _multipleLocation()
    {
        $lng_diff = 121.43 - 121.06;
        $lat_diff = 31.21 - 30.55;
        $lng_lat = array();
        for($i = 0; $i < 100; $i++) {
            $lng_lat[] = array('Longitude' => 120.51 + $lng_diff * lcg_value(), 'Latitude' => 30.40 + $lat_diff * lcg_value());
        }

        return $lng_lat;
    }
}

