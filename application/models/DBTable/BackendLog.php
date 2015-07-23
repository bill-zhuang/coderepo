<?php

class Application_Model_DBTable_BackendLog extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('backend_log');
    }

    public function getBackendLogCount(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'count(*) as total');
        foreach ($conditions as $key => $content)
        {
            $select->where($key . ' ' . $content['compare_type'], $content['value']);
        }
        $count = $select->query()->fetchAll();
        return $count[0]['total'];
    }

    public function getBackendLogData(array $conditions, $count, $offset, $order_by)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $key => $content)
        {
            $select->where($key . ' ' . $content['compare_type'], $content['value']);
        }
        $data = $select
            ->limit($count, $offset)
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

    public function writeLog($type, $table_name, $data, $where = '')
    {
        $sql = '';
        switch($type)
        {
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

        if ($sql != '')
        {
            $user_id = isset(Application_Model_Auth::getIdentity()->bu_id) ?
                Application_Model_Auth::getIdentity()->bu_id : 0;
            $date_time = date('Y-m-d H:i:s');
            $insert_data = [
                'content' => $sql,
                'buid' => $user_id,
                'status' => 1,
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
        foreach ($bind as $column_name => $column_value)
        {
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
        foreach ($bind as $column_name => $column_value)
        {
            if ($column_value instanceof Zend_Db_Expr)
            {
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
        $sql = 'delete from' . $table . ' where ' . $where . ';';

        return $sql;
    }

    /*
     * get from zend db abstract _whereExpr method
     * */
    private function _processWhere($where)
    {
        if (empty($where))
        {
            return $where;
        }
        if (!is_array($where))
        {
            $where = array($where);
        }
        foreach ($where as $cond => &$term)
        {
            // is $cond an int? (i.e. Not a condition)
            if (is_int($cond))
            {
                // $term is the full condition
                if ($term instanceof Zend_Db_Expr)
                {
                    $term = $term->__toString();
                }
            }
            else
            {
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