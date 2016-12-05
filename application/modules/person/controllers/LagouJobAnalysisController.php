<?php

class person_LagouJobAnalysisController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_LagouCategory
     */
    private $_adapterLagouCategory;
    /**
     * @var Application_Model_DBTable_LagouCity
     */
    private $_adapterLagouCity;
    /**
     * @var Application_Model_DBTable_LagouJob
     */
    private $_adapterLagouJob;
    /**
     * @var Application_Model_DBTable_LagouJobAnalysis
     */
    private $_adapterLagouJobAnalysis;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapterLagouCategory = new Application_Model_DBTable_LagouCategory();
        $this->_adapterLagouCity = new Application_Model_DBTable_LagouCity();
        $this->_adapterLagouJob = new Application_Model_DBTable_LagouJob();
        $this->_adapterLagouJobAnalysis = new Application_Model_DBTable_LagouJobAnalysis();
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
    }

    public function ajaxIndexAction()
    {
        echo json_encode($this->_index());
    }

    private function _index()
    {
        $params = $this->_getParam('params', []);
        $startDate = (isset($params['startDate']) && Bill_Util::validDate($params['startDate']))
            ? trim($params['startDate']) : date('Y-m-d', strtotime('-1 month'));
        $endDate = (isset($params['endDate']) && Bill_Util::validDate($params['endDate']))
            ? trim($params['endDate']) : date('Y-m-d');
        $mainCaid = isset($params['mainCaid']) ? intval($params['mainCaid']) : 0;
        $subCaid = isset($params['subCaid']) ? intval($params['subCaid']) : 0;
        $joid = isset($params['joid']) ? intval($params['joid']) : 0;
        $lgCtid = isset($params['lgCtid']) ? intval($params['lgCtid']) : 0;

        if($joid == 0 && $lgCtid == 0) {
            $jsonData = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, 'Job和城市不能同时为全部')
            ];
            return $jsonData;
        }

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS,
        ];
        if ($startDate !== '') {
            $conditions['date >=?'] = $startDate;
        }
        if ($endDate !== '') {
            $conditions['date <=?'] = $endDate;
        }
        $joids = $this->_getJoidByCaid($mainCaid, $subCaid, $joid);
        if (!empty($joids)) {
            $conditions['joid in (?)'] = $joids;
        } else {
            $conditions['1 =?'] = 0;
        }
        if ($lgCtid > 0) {
            $conditions['lg_ctid =?'] = $lgCtid;
        }

        $data = $this->_adapterLagouJobAnalysis->getJobAnalysisData($conditions);

        $hashTable = [];
        foreach ($data as $value) {
            $hashKey = $value['joid'] . '-' . $value['lg_ctid'];
            if (!isset($hashTable[$hashKey])) {
                $hashTable[$hashKey] = [];
            }
            $hashTable[$hashKey][] = [
                strtotime($value['date'] . ' 08:00:00') * 1000,
                intval($value['num']),
            ];
        }

        $chartData = [];
        foreach ($hashTable as $hashKey => $hashValue) {
            list($chartJoid, $chartCtid) = explode('-', $hashKey);
            $chartName = $this->_adapterLagouJob->getNameByJoid($chartJoid) . '-'
                . $this->_adapterLagouCity->getNameByLgCtid($chartCtid);
            $chartData[] = [
                'name' => $chartName,
                'data' => $hashValue,
            ];
        }

        $jsonData = [
            'searchData' => [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'mainCaid' => $mainCaid,
                'subCaid' => $subCaid,
                'joid' => $joid,
                'lgCtid' => $lgCtid,
            ],
            'data' => $chartData,
        ];
        return $jsonData;
    }

    private function _getJoidByCaid($mainCaid, $subCaid, $joid)
    {
        if ($joid > 0) {
            $joids = [$joid];
        } else {
            $conditions = [
                'status =?' => Bill_Constant::VALID_STATUS,
            ];
            if ($subCaid > 0) {
                $conditions['caid =?'] = $subCaid;
            } else if ($mainCaid > 0) {
                $subCaids = $this->_adapterLagouCategory->getAllSubCaids($mainCaid);
                if (!empty($subCaids)) {
                    $conditions['caid in (?)'] = $subCaids;
                } else {
                    $conditions['1 =?'] = 0;
                }
            }
            $joids = $this->_adapterLagouJob->getJoidsByCondition($conditions);
        }

        return $joids;
    }
}
