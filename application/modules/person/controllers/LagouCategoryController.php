<?php

class person_LagouCategoryController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_LagouCategory
     */
    private $_adapterLagouCategory;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapterLagouCategory = new Application_Model_DBTable_LagouCategory();
    }

    public function getMainCategoryAction()
    {
        $data = $this->_adapterLagouCategory->getAllMainCategory();
        $jsonData = [
            'data' => [
                'currentItemCount' => count($data),
                'items' => $data,
            ],
        ];
        echo json_encode($jsonData);
    }

    public function getSubCategoryAction()
    {
        $jsonArray = [];
        if (isset($_GET['params'])) {
            $params = $this->_getParam('params', []);
            $pid = isset($params['pid']) ? intval($params['pid']) : Bill_Constant::INVALID_PRIMARY_ID;
            $subcategoryData = $this->_adapterLagouCategory->getAllSubCategoryByPid($pid);
            if (!empty($subcategoryData)) {
                $jsonArray = [
                    'data' => [
                        'currentItemCount' => count($subcategoryData),
                        'items' => $subcategoryData,
                    ],
                ];
            }
        }

        if (!isset($jsonArray['data'])) {
            $jsonArray = [
                'error' => Bill_Util::getJsonResponseErrorArray(200, Bill_Constant::ACTION_ERROR_INFO),
            ];
        }

        echo json_encode($jsonArray);
    }
}
