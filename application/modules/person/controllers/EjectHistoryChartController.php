<?php

class person_EjectHistoryChartController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_EjectHistory
     */
    private $_adapter_eject_history;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_adapter_eject_history= new Application_Model_DBTable_EjectHistory();
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
    }

    public function ajaxEjectHistoryPeriodAction()
    {
        $params = $this->_getParam('params', []);
        $start_date = (isset($params['day_start_date']) && Bill_Util::validDate($params['day_start_date']))
            ? trim($params['day_start_date']) : date('Y-m-d', strtotime('-1 year'));
        $end_date = (isset($params['day_end_date']) && Bill_Util::validDate($params['day_end_date']))
            ? trim($params['day_end_date']) : '';
        $data = [];
        $types = $this->_getEjectTypes();
        foreach ($types as $type_name => $type) {
            $eject_data = $this->_adapter_eject_history->getTotalEjectHistoryDataByDay($start_date, $end_date, $type);
            $type_data = [];
            foreach ($eject_data as $eject_value) {
                $type_data[] = [
                    strtotime($eject_value['period']) * 1000,
                    intval($eject_value['number']),
                ];
            }
            $data[] = [
                'name' => $type_name,
                'data' => $type_data,
            ];
        }

        $json_array = [
            'data' => $data
        ];

        echo json_encode($json_array);
    }

    public function ajaxEjectHistoryMonthAction()
    {
        $params = $this->_getParam('params', []);
        $start_date = (isset($params['month_start_date']) && Bill_Util::validDate($params['month_start_date']))
            ? trim($params['month_start_date']) : date('Y-m', strtotime('-11 month')) . '-01';
        $end_date = (isset($params['month_end_date']) && Bill_Util::validDate($params['month_end_date']))
            ? trim($params['month_end_date']) : '';
        $data = [
            'months' => [],
            'data' => [],
        ];

        $types = $this->_getEjectTypes();
        $months = [];
        $temp_data = [];
        foreach ($types as $type_name => $type) {
            $type_data = [];
            $month_data = $this->_adapter_eject_history->getTotalEjectHistoryGroupData($start_date, $end_date, $type);
            foreach ($month_data as $month_value) {
                $type_data[] = intval($month_value['number']);
                if (!in_array($month_value['period'], $months)) {
                    $months[] = $month_value['period'];
                }
                $type_data[$month_value['period']] = $month_value['number'];
            }
            $temp_data[$type_name] = $type_data;
        }

        $data['months'] = $this->_getMonthsRange($months);

        foreach ($types as $type_name => $type) {
            $type_data = [];
            foreach ($data['months'] as $month) {
                if (isset($temp_data[$type_name][$month])) {
                    $type_data[] = intval($temp_data[$type_name][$month]);
                } else {
                    $type_data[] = 0;
                }
            }
            $data['data'][] = [
                'name' => $type_name,
                'data' => $type_data,
            ];
        }
        $json_array = [
            'data' => $data
        ];

        echo json_encode($json_array);
    }

    private function _getEjectTypes()
    {
        $types = [
            'Dream' => Bill_Constant::EJECT_TYPE_DREAM,
            'Bad' => Bill_Constant::EJECT_TYPE_BAD,
        ];
        return $types;
    }

    private function _getMonthsRange(array $months)
    {
        if (count($months) > 2) {
            sort($months);
            $data['months'] = $months;
            $min_month = $months[0];
            $max_month = $months[count($months) - 1];
            $data['months'][] = $months[0];
            for ($i = 1; ; $i++) {
                $next_month = date('Y-m', strtotime($min_month . "+ {$i} month"));
                if (strtotime($next_month) <= strtotime($max_month)) {
                    $months[] = $next_month;
                } else {
                    break;
                }
            }
            sort($months);
        }

        return $months;
    }
}
