<?php

class person_EjectHistoryChartController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_EjectHistory
     */
    private $_adapterEjectHistory;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->_adapterEjectHistory = new Application_Model_DBTable_EjectHistory();
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
        $this->getResponse()->setHeader('Content-Type', 'text/html');
    }

    public function ajaxEjectHistoryPeriodAction()
    {
        $params = $this->_getParam('params', []);
        $startDate = (isset($params['day_start_date']) && Bill_Util::validDate($params['day_start_date']))
            ? trim($params['day_start_date']) : date('Y-m-d', strtotime('-1 year'));
        $endDate = (isset($params['day_end_date']) && Bill_Util::validDate($params['day_end_date']))
            ? trim($params['day_end_date']) : '';
        $data = [];
        $types = $this->_getEjectTypes();
        foreach ($types as $typeName => $type) {
            $ejectData = $this->_adapterEjectHistory->getTotalEjectHistoryDataByDay($startDate, $endDate, $type);
            $typeData = [];
            $previousTimestamp = 0;
            foreach ($ejectData as $ejctKey => $ejectValue) {
                $currentTimestamp = strtotime($ejectValue['period'] . ' 08:00:00');
                $typeData[] = [
                    $currentTimestamp * 1000,
                    ($ejctKey > 0 ? (intval($currentTimestamp - $previousTimestamp) / Bill_Constant::DAY_SECONDS) : 0),
                ];
                $previousTimestamp = $currentTimestamp;
            }
            $data[] = [
                'name' => $typeName,
                'data' => $typeData,
            ];
        }

        $jsonArray = [
            'searchData' => [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ],
            'data' => $data
        ];

        echo json_encode($jsonArray);
    }

    public function ajaxEjectHistoryMonthAction()
    {
        $params = $this->_getParam('params', []);
        $startDate = (isset($params['month_start_date']) && Bill_Util::validDate($params['month_start_date']))
            ? trim($params['month_start_date']) : date('Y-m', strtotime('-11 month')) . '-01';
        $endDate = (isset($params['month_end_date']) && Bill_Util::validDate($params['month_end_date']))
            ? trim($params['month_end_date']) : date('Y-m-d');
        $data = [
            'months' => [],
            'data' => [],
        ];

        $types = $this->_getEjectTypes();
        $months = [];
        $tempData = [];
        foreach ($types as $typeName => $type) {
            $typeData = [];
            $monthData = $this->_adapterEjectHistory->getTotalEjectHistoryGroupData($startDate, $endDate, $type);
            foreach ($monthData as $monthValue) {
                $typeData[] = intval($monthValue['number']);
                if (!in_array($monthValue['period'], $months)) {
                    $months[] = $monthValue['period'];
                }
                $typeData[$monthValue['period']] = $monthValue['number'];
            }
            $tempData[$typeName] = $typeData;
        }

        $data['months'] = Bill_Util::getMonthsByStartEnd($startDate, $endDate);

        foreach ($types as $typeName => $type) {
            $typeData = [];
            foreach ($data['months'] as $month) {
                if (isset($tempData[$typeName][$month])) {
                    $typeData[] = intval($tempData[$typeName][$month]);
                } else {
                    $typeData[] = 0;
                }
            }
            $data['data'][] = [
                'name' => $typeName,
                'data' => $typeData,
            ];
        }
        $jsonArray = [
            'searchData' => [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ],
            'data' => $data
        ];

        echo json_encode($jsonArray);
    }

    private function _getEjectTypes()
    {
        $types = [
            'Dream' => Bill_Constant::EJECT_TYPE_DREAM,
            'Bad' => Bill_Constant::EJECT_TYPE_BAD,
        ];
        return $types;
    }
}
