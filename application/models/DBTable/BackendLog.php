<?php

class Application_Model_DBTable_BackendLog extends Zend_Db_Table_Abstract
{
    public function __construct($section_name = Bill_Constant::LOCAL_DB)
    {
        $config = [
            Zend_Db_Table_Abstract::ADAPTER => Application_Model_DBAdapter::getDBAdapter($section_name),
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

    public function getBackendLogData(array $conditions, $startPage, $pageLength, $order_by)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $data = $select
            ->limitPage($startPage, $pageLength)
            ->order($order_by)
            ->query()->fetchAll();
        return $data;
    }

    public function getBackendLogByID($blid)
    {
        return $this->select()->reset()
            ->where('blid=?', $blid)
            ->query()->fetch();
    }

    public function getAllBlidAndContent()
    {
        $data = $this->select()->reset()
            ->from($this->_name, ['blid', 'content', 'update_time'])
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        return $data;
    }

    public function writeLog($type, $table_name, $data, $where = '')
    {
        $sql = '';
        switch($type) {
            case 'insert':
                $sql = $this->_getInsertSQL($table_name, $data);
                break;
            case 'update':
                $sql = $this->_getUpdateSQL($table_name, $data, $where);
                break;
            case 'delete':
                $sql = $this->_getDeleteSQL($table_name, $where);
                break;
            default:
                break;
        }

        if ($sql != '') {
            $user_id = isset(Application_Model_Auth::getIdentity()->buid) ?
                Application_Model_Auth::getIdentity()->buid : Bill_Constant::INVALID_PRIMARY_ID;
            $date_time = date('Y-m-d H:i:s');
            $insert_data = [
                'type' => $type,
                'table' => $table_name,
                'content' => $sql,
                'buid' => $user_id,
                'status' => Bill_Constant::VALID_STATUS,
                'create_time' => $date_time,
                'update_time' => $date_time
            ];
            parent::insert($insert_data);
        }
    }

    private function _getInsertSQL($table, array $bind)
    {
        $sql = 'insert into ' . $table . '(';
        $names = '';
        $values = '';
        foreach ($bind as $column_name => $column_value) {
            $names .= $column_name . ',';
            $values .= '\'' . addslashes($column_value) . '\',';
        }
        $names = substr($names, 0, -1);
        $values = substr($values, 0, -1);
        $sql = $sql . $names . ') values (' . $values . ');';

        return $sql;
    }

    private function _getUpdateSQL($table, array $bind, $where)
    {
        $sql = 'update ' . $table . ' set ';
        $set_sql = '';
        foreach ($bind as $column_name => $column_value) {
            if ($column_value instanceof Zend_Db_Expr) {
                $column_value = $column_value->__toString();
            }
            $set_sql .= $column_name . '=\'' . addslashes($column_value) . '\',';
        }
        $set_sql = substr($set_sql, 0, -1);
        $sql .= $set_sql . ' where ' . $this->_processWhere($where) . ';';

        return $sql;
    }

    private function _getDeleteSQL($table, $where)
    {
        $sql = 'delete from' . $table . ' where ' . $this->_processWhere($where) . ';';

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