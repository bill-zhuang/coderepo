<?php

class person_LagouJobController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_LagouCategory
     */
    private $_adapterLagouCategory;
    /**
     * @var Application_Model_DBTable_LagouJob
     */
    private $_adapterLagouJob;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapterLagouCategory = new Application_Model_DBTable_LagouCategory();
        $this->_adapterLagouJob = new Application_Model_DBTable_LagouJob();
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
        list($currentPage, $pageLength, $start) = Bill_Util::getPaginationParamsFromUrlParamsArray($params);
        $mainCaid = isset($params['mainCaid']) ? intval($params['mainCaid']) : 0;
        $subCaid = isset($params['subCaid']) ? intval($params['subCaid']) : 0;

        $conditions = [
            'status =?' => Bill_Constant::VALID_STATUS
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
        $orderBy = 'joid ASC';
        $total = $this->_adapterLagouJob->getSearchCount($conditions);
        $data = $this->_adapterLagouJob->getSearchData($conditions, $currentPage, $pageLength, $orderBy);
        foreach ($data as &$value) {
            list($subName, $pid) = $this->_adapterLagouCategory->getNamePid($value['caid']);
            $value['main'] = $this->_adapterLagouCategory->getCategoryName($pid);
            $value['sub'] = $subName;
        }

        $jsonData = [
            'data' => [
                'totalPages' => Bill_Util::getTotalPages($total, $pageLength),
                'pageIndex' => $currentPage,
                'totalItems' => $total,
                'startIndex' => $start + 1,
                'itemsPerPage' => $pageLength,
                'currentItemCount' => count($data),
                'items' => $data,
            ],
        ];
        return $jsonData;
    }
    
}
