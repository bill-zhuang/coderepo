<?php

class Application_Model_DBTable_BackendLog extends Zend_Db_Table_Abstract
{
    public function __construct($sectionName = Bill_Constant::LOCAL_DB)
    {
        $config = [
            Zend_Db_Table_Abstract::ADAPTER => Application_Model_DBAdapter::getDBAdapter($sectionName),
            Zend_Db_Table_Abstract::NAME => 'backend_log'
        ];
        parent::__construct($config);
    }

    public function getBackendLogCount(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'count(*) as total');
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return $count[0]['total'];
    }

    public function getBackendLogData(array $conditions, $startPage, $pageLength, $orderBy)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $data = $select
            ->limitPage($startPage, $pageLength)
            ->order($orderBy)
            ->query()->fetchAll();
        return $data;
    }

    public function writeLog($type, $tableName, $data, $where = '')
    {
        $sql = '';
        switch($type) {
            case 'insert':
                $sql = $this->_getInsertSQL($tableName, $data);
                break;
            case 'update':
                $sql = $this->_getUpdateSQL($tableName, $data, $where);
                break;
            case 'delete':
                $sql = $this->_getDeleteSQL($tableName, $where);
                break;
            default:
                break;
        }

        if ($sql != '') {
            $userId = isset(Application_Model_Auth::getIdentity()->buid) ?
                Application_Model_Auth::getIdentity()->buid : Bill_Constant::INVALID_PRIMARY_ID;
            $dateTime = date('Y-m-d H:i:s');
            $insertData = [
                'type' => $type,
                'table' => $tableName,
                'content' => $sql,
                'buid' => $userId,
                'status' => Bill_Constant::VALID_STATUS,
                'create_time' => $dateTime,
                'update_time' => $dateTime
            ];
            parent::insert($insertData);
        }
    }

    private function _getInsertSQL($table, array $bind)
    {
        $sql = 'insert into ' . $table . '(';
        $names = '';
        $values = '';
        foreach ($bind as $columnName => $columnValue) {
            $names .= $columnName . ',';
            $values .= '\'' . addslashes($columnValue) . '\',';
        }
        $names = substr($names, 0, -1);
        $values = substr($values, 0, -1);
        $sql = $sql . $names . ') values (' . $values . ');';

        return $sql;
    }

    private function _getUpdateSQL($table, array $bind, $where)
    {
        $sql = 'update ' . $table . ' set ';
        $setSql = '';
        foreach ($bind as $columnName => $columnValue) {
            if ($columnValue instanceof Zend_Db_Expr) {
                $columnValue = $columnValue->__toString();
            }
            $setSql .= $columnName . '=\'' . addslashes($columnValue) . '\',';
        }
        $setSql = substr($setSql, 0, -1);
        $sql .= $setSql . ' where ' . $this->_processWhere($where) . ';';

        return $sql;
    }

    private function _getDeleteSQL($table, $where)
    {
        $sql = 'delete from ' . $table . ' where ' . $this->_processWhere($where) . ';';

        return $sql;
    }

    /*
     * get from zend db abstract _whereExpr method
     * */
    private function _processWhere($where)
    {
        if (empty($where)) {
            return $where;
        }
        if (!is_array($where)) {
            $where = array($where);
        }
        foreach ($where as $cond => &$term) {
            // is $cond an int? (i.e. Not a condition)
            if (is_int($cond)) {
                // $term is the full condition
                if ($term instanceof Zend_Db_Expr) {
                    $term = $term->__toString();
                }
            } else {
                // $cond is the condition with placeholder,
                // and $term is quoted into the condition
                $term = str_replace('?', $this->getAdapter()->quote($term, null), $cond);
            }
            $term = '(' . $term . ')';
        }

        $where = implode(' AND ', $where);
        return $where;
    }
}