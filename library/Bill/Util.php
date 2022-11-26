<?php

class Bill_Util
{
    public static function encodeChineseCharacterInUrl($url)
    {
        return preg_replace_callback(Bill_Regex::CHINESE_CHARACTER, function ($matches) {
                return urlencode($matches[0]);
            }, trim($url));
    }

    public static function extractImageBase64Content($content)
    {
        $pregImg = '/<img.*?src="([^"]+)"/';
        $pregImageBase64 = '/^data.*?64,/';
        $isMatch = preg_match_all($pregImg, $content, $matches);
        if ($isMatch > 0) {
            $imageBase64Contents = array();
            foreach ($matches[1] as $value) {
                if (substr($value, 0, 4) != 'http') {
                    $imageBase64Contents[] = $value;
                }
            }
            if (!empty($imageBase64Contents)) {
                foreach ($imageBase64Contents as $imageBase64Content) {
                    //return base64_decode(preg_replace($pregImageBase64, '', $imageBase64Content));
                    //save image or upload image here
                }
            }
        }

        return $content;
    }

    public static function validDate($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') == $date;
    }

    public static function getMonthRange(array $months)
    {
        if (count($months) > 2) {
            sort($months);
            $minMonth = $months[0];
            $maxMonthTimestamp = strtotime($months[count($months) - 1]);
            for ($i = 1; ; $i++) {
                $nextMonth = date('Y-m', strtotime($minMonth . "+ {$i} month"));
                if ((strtotime($nextMonth) <= $maxMonthTimestamp)) {
                    if (!in_array($nextMonth, $months)) {
                        $months[] = $nextMonth;
                    }
                } else {
                    break;
                }
            }
            sort($months);
        }

        return $months;
    }

    public static function getMonthsByStartEnd($start, $end)
    {
        $months = [];
        while (strtotime($start) <= strtotime($end)) {
            $months[] = date('Y-m', strtotime($start));
            //
            $start = date('Y-m-d', strtotime($start . "+ 1 month"));
        }

        return $months;
    }

    public static function isProductionEnv()
    {
        return ($_SERVER['HTTP_HOST'] == Bill_Constant::PRODUCTION_HOST) ? true : false;
    }

    public static function isAlphaEnv()
    {
        return ($_SERVER['HTTP_HOST'] == Bill_Constant::ALPHA_HOST) ? true : false;
    }

    public static function isCommandLineInterface()
    {
        return (php_sapi_name() == 'cli' || defined('STDIN'));
    }

    public static function isAjaxRequest()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    public static function handleException($exception, $from)
    {
        $title = trim($from);
        if ($exception instanceof Exception) {
            $content = $exception->getMessage() . Bill_Html::br() . $exception->getTraceAsString();
        } else {
            $content = trim($exception);
        }
        Bill_Mail::send($title, $content);
    }

    public static function getJsonResponseErrorArray($errorCode, $errorMessage)
    {
        return [
            'code' => $errorCode,
            'message' => $errorMessage,
        ];
    }

    public static function getPaginationParamsFromUrlParamsArray(array $params)
    {
        $currentPage = (isset($params['current_page']) && intval($params['current_page']) > 0)
            ? intval($params['current_page']) : Bill_Constant::INIT_START_PAGE;
        $pageLength = (isset($params['page_length']) && intval($params['page_length']) > 0)
            ? intval($params['page_length']) : Bill_Constant::INIT_PAGE_LENGTH;
        $start = ($currentPage - Bill_Constant::INIT_START_PAGE) * $pageLength;
        return [
            $currentPage,
            $pageLength,
            $start
        ];
    }

    public static function getTotalPages($totalItems, $itemsPerPage)
    {
        return ceil($totalItems / $itemsPerPage) ? ceil($totalItems / $itemsPerPage) : Bill_Constant::INIT_TOTAL_PAGE;
    }

    public static function getLikeString($keyword, $likeType = Bill_Constant::LIKE_FULL)
    {
        switch($likeType) {
            case Bill_Constant::LIKE_FULL:
                $keyword = '%' . $keyword . '%';
                break;
            case Bill_Constant::LIKE_LEFT:
                $keyword = '%' . $keyword;
                break;
            case Bill_Constant::LIKE_RIGHT:
                $keyword = $keyword . '%';
                break;
            default:
                $keyword = '%' . $keyword . '%';
                break;
        }
        return $keyword;
    }

    public static function getAclMapKey($module, $controller, $action)
    {
        return $module . '_' . $controller . '_' . $action;
    }

    /**
     * @param $logPath string log file absolute path
     * @param $mode string function fopen param mode
     * @param $content string log content
     */
    public static function writeLog($logPath, $mode, $content)
    {
        $handle = fopen($logPath, $mode);
        if ($handle !== false) {
            flock($handle, LOCK_EX);
            fwrite($handle, $content);
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    public static function printSQL()
    {
        $queryContent = self::getSQLInfo();

        echo '<pre>';
        print_r($queryContent);
    }

    public static function getSQLInfo()
    {
        $queryContent = array();
        $adapter = Application_Model_DBAdapter::getDBAdapter();
        if ($adapter instanceof Zend_Db_Adapter_Abstract) {
            $profiler = $adapter->getProfiler();
            $queryNum = $profiler->getTotalNumQueries();
            if ($queryNum > 0) {
                $queryContent['queryNum'] = $queryNum;
                $queryContent['queryCost'] = $profiler->getTotalElapsedSecs();
                //
                $queries = $profiler->getQueryProfiles();
                $queryDetail = array();
                foreach ($queries as $query) {
                    if ($query instanceof Zend_Db_Profiler_Query) {
                        $queryDetail[] = array(
                            'sql' => $query->getQuery(),
                            'second' => $query->getElapsedSecs(),
                        );
                    }
                }

                $queryContent['queryDetail'] = $queryDetail;
            }
        }

        return $queryContent;
    }
} 