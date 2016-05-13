<?php

class GoogleMapController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
    }
    
    public function markLocationAction()
    {
        $jsonArray = [];
        $params = $this->_getParam('params', []);
        if(isset($params['location'])) {
            $coordinateInfo = Bill_GoogleMap::getLngLatByAddress($params['location']);
            if (!empty($coordinateInfo)) {
                $jsonArray['data'] = $coordinateInfo;
            } else {
                $jsonArray['error'] = Bill_Util::getJsonResponseErrorArray('200', 'Fetch Location coordinate failed.');
            }
        } else {
            $jsonArray['error'] = Bill_Util::getJsonResponseErrorArray('200', 'Param location not provided.');
        }
        
        echo json_encode($jsonArray);
    }

    public function multipleLocationAction()
    {
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
    }

    public function ajaxMultipleLocationAction()
    {
        $jsonArray = [
            'data' => [
                'coordinates' => $this->_multipleLocation()
            ],
        ];
        echo json_encode($jsonArray);
    }

    private function _multipleLocation()
    {
        $lngDiff = 121.43 - 121.06;
        $latDiff = 31.21 - 30.55;
        $lngLat = array();
        for($i = 0; $i < 100; $i++) {
            $lngLat[] = array('Longitude' => 120.51 + $lngDiff * lcg_value(), 'Latitude' => 30.40 + $latDiff * lcg_value());
        }

        return $lngLat;
    }
}

