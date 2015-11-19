<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-12-8
 * Time: 下午1:30
 */

class Application_Model_DBTableFactory extends Zend_Db_Table_Abstract
{
    private $_table_name;

    public function __construct($table_name, $section_name = 'localdb')
    {
        $this->_table_name = $table_name;
        $config = [
            'db' => Application_Model_DBAdapter::getDBAdapter($section_name),
            'name' => $table_name
        ];
        parent::__construct($config);
    }

    public function insert(array $data)
    {
        $adapter_backend_log = new Application_Model_DBTable_BackendLog();
        $adapter_backend_log->writeLog('insert', $this->_table_name, $data);
        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $adapter_backend_log = new Application_Model_DBTable_BackendLog();
        $adapter_backend_log->writeLog('update', $this->_table_name, $data, $where);
        return parent::update($data, $where);
    }

    public function delete($where)
    {
        $adapter_backend_log = new Application_Model_DBTable_BackendLog();
        $adapter_backend_log->writeLog('delete', $this->_table_name, [], $where);
        return parent::delete($where);
    }
}