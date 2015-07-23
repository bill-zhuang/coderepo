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
        $lng_lat = array();
        if($_GET && $_GET['location'])
        {
        	$lng_lat = Bill_GoogleMap::getLngLatByAddress($_GET['location']);
        }
        
        echo json_encode($lng_lat);
        exit;
    }

    public function multipleLocationAction()
    {

    }

    public function ajaxMultipleLocationAction()
    {
        echo json_encode($this->_multipleLocation());
        exit;
    }

    private function _multipleLocation()
    {
        $lng_diff = 121.43 - 121.06;
        $lat_diff = 31.21 - 30.55;
        $lng_lat = array();
        for($i = 0; $i < 100; $i++)
        {
            $lng_lat[] = array('Longitude' => 120.51 + $lng_diff * lcg_value(), 'Latitude' => 30.40 + $lat_diff * lcg_value());
        }

        return $lng_lat;
    }
}

