<?php

class person_LagouCityController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_LagouCity
     */
    private $_adapterLagouCity;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapterLagouCity = new Application_Model_DBTable_LagouCity();
    }

    public function getCityListAction()
    {
        $params = $this->_getParam('params', []);
        $firstLetter = isset($params['firstLetter']) ? trim($params['firstLetter']) : '';
        if ($firstLetter !== '') {
            $firstLetter = strtoupper($firstLetter);
            $cityList = $this->_adapterLagouCity->getCityListByFirstLetter($firstLetter);
            $data = [
                'data' => [
                    'currentItemCount' => count($cityList),
                    'items' => $cityList,
                ]
            ];
        }

        if (!isset($data['data'])) {
            $data = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($data);
    }
}
