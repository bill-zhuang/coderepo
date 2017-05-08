<?php

class person_EtfFundAnalysisController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_EtfFund
     */
    private $_adapterEtfFund;
    /**
     * @var Application_Model_DBTable_EtfFundAnalysis
     */
    private $_adapterEtfFundAnalysis;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->_adapterEtfFund = new Application_Model_DBTable_EtfFund();
        $this->_adapterEtfFundAnalysis = new Application_Model_DBTable_EtfFundAnalysis();
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
        $this->getResponse()->setHeader('Content-Type', 'text/html');
    }

    public function ajaxIndexAction()
    {
        echo json_encode($this->_index());
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        $startDate = (isset($params['startDate']) && Bill_Util::validDate($params['startDate']))
            ? trim($params['startDate']) : date('Y-m-d', strtotime('-3 years'));
        $endDate = (isset($params['endDate']) && Bill_Util::validDate($params['endDate']))
            ? trim($params['endDate']) : date('Y-m-d');
        $fuid = isset($params['fuid']) ? intval($params['fuid']) : 0;

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
        ];
        if ($startDate !== '') {
            $conditions['date >=?'] = $startDate;
        }
        if ($endDate !== '') {
            $conditions['date <=?'] = $endDate;
        }
        if ($fuid > 0) {
            $conditions['fuid =?'] = $fuid;
        } else {
            $conditions['1=?'] = 0;
        }

        $data = $this->_adapterEtfFundAnalysis->getFundAnalysisData($conditions);
        $unitNetData = [];
        $accumNetData = [];
        foreach ($data as $value) {
            $unitNetData[] = [
                strtotime($value['date'] . ' 08:00:00') * 1000,
                floatval($value['unit_net_value']),
            ];
            $accumNetData[] = [
                strtotime($value['date'] . ' 08:00:00') * 1000,
                floatval($value['accum_net_value']),
            ];
        }

        $jsonData = [
            'searchData' => [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'fuid' => $fuid,
            ],
            'data' => [
                'unitNetData' => $unitNetData,
                'accumNetData' => $accumNetData,
            ],
        ];
        return $jsonData;
    }
    
}
