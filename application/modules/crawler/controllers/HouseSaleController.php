<?php

class crawler_HouseSaleController extends Zend_Controller_Action
{
    /**
     * @var Application_Model_DBTable_HouseSale
     */
    private $_adapterHouseSale;
    /**
     * @var Application_Model_DBTable_CnnbfdcSale
     */
    private $_adapterCnnbfdcSale;

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->_adapterHouseSale = new Application_Model_DBTable_HouseSale();
        $this->_adapterCnnbfdcSale = new Application_Model_DBTable_CnnbfdcSale();
    }

    public function indexAction()
    {
        // action body
        $this->_helper->layout()->enableLayout();
        $this->_helper->viewRenderer->setNoRender(false);
        $this->getResponse()->setHeader('Content-Type', 'text/html');
    }

    public function ajaxHouseSaleDayAction()
    {
        $params = $this->_getParam('params', []);
        $startDate = (isset($params['day_start_date']) && Bill_Util::validDate($params['day_start_date']))
            ? trim($params['day_start_date']) : date('Y-m-d', strtotime('-1 month'));
        $endDate = (isset($params['day_end_date']) && Bill_Util::validDate($params['day_end_date']))
            ? trim($params['day_end_date']) : date('Y-m-d');
        $data = [];
        $dayInterval = intval((strtotime($endDate) - strtotime($startDate)) / 86400);
        for($i = 0; $i <= $dayInterval; $i++) {
            $periodDate = date('Y-m-d', strtotime($startDate . " + {$i} day"));
            $data[$periodDate] = 0;
        }
        $lejuData = [];
        $dayData = $this->_adapterHouseSale->getSaleDataByDay($startDate, $endDate);
        foreach ($dayData as $dayValue) {
            $lejuData[] = [
                strtotime($dayValue['period'] . ' 08:00:00') * 1000,
                floatval($dayValue['sales']),
            ];
        }
        $officialData = [];
        $officialPriceData = [];
        $officialDayData = $this->_adapterCnnbfdcSale->getSaleAndAveragePriceDataByDay($startDate, $endDate);
        foreach ($officialDayData as $officialDayValue) {
            $officialPeriod = strtotime($officialDayValue['period'] . ' 08:00:00') * 1000;
            $officialData[] = [
                $officialPeriod,
                floatval($officialDayValue['sales']),
            ];
            $officialPriceData[] = [
                $officialPeriod,
                floatval($officialDayValue['avgPrice']),
            ];
        }

        $jsonArray = [
            'searchData' => [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ],
            'data' => [
                'leju' => $lejuData,
                'official' => $officialData,
                'price' => $officialPriceData,
            ],
        ];
        echo json_encode($jsonArray);
    }

    public function ajaxHouseSaleMonthAction()
    {
        $params = $this->_getParam('params', []);
        $startDate = (isset($params['month_start_date']) && Bill_Util::validDate($params['month_start_date']))
            ? trim($params['month_start_date']) : date('Y-m', strtotime('-11 month')) . '-01';
        $endDate = (isset($params['month_end_date']) && Bill_Util::validDate($params['month_end_date']))
            ? trim($params['month_end_date']) : '';
        $data = [
            'months' => [],
            'data' => [],
        ];
        $tempData = [];
        $monthData = $this->_adapterHouseSale->getSaleDataByMonth($startDate, $endDate);
        foreach ($monthData as $monthValue) {
            $data['months'][] = $monthValue['period'];
            $tempData[$monthValue['period']] = $monthValue['sales'];
        }
        $data['months'] = Bill_Util::getMonthRange($data['months']);
        foreach ($data['months'] as $month) {
            if (isset($tempData[$month])) {
                $data['data'][] = intval($tempData[$month]);
            } else {
                $data['data'][] = 0;
            }
        }
        $jsonArray = [
            'searchData' => [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ],
            'data' => $data,
        ];

        echo json_encode($jsonArray);
    }
    
}
