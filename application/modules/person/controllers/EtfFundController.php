<?php

class person_EtfFundController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_EtfFund
     */
    private $_adapterEtfFund;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->_adapterEtfFund = new Application_Model_DBTable_EtfFund();
    }
    public function getFundListAction()
    {
        $fundList = $this->_adapterEtfFund->getFundList();
        $data = [
            'data' => [
                'currentItemCount' => count($fundList),
                'items' => $fundList,
            ]
        ];

        if (!isset($data['data'])) {
            $data = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($data);
    }
}
