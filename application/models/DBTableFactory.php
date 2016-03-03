<?php
/**
 * Created by bill-zhuang.
 * User: bill-zhuang
 * Date: 14-12-8
 * Time: 下午1:30
 */

class Application_Model_DBTableFactory extends Zend_Db_Table_Abstract
{
    private $_table_name;
    private $_adapter_backend_log;

    public function __construct($table_name, $section_name = Bill_Constant::LOCAL_DB)
    {
        $this->_table_name = $table_name;
        $config = [
            Zend_Db_Table_Abstract::ADAPTER => Application_Model_DBAdapter::getDBAdapter($section_name),
            Zend_Db_Table_Abstract::NAME => $this->_table_name,
        ];
        parent::__construct($config);
        $this->_adapter_backend_log = new Application_Model_DBTable_BackendLog();
    }

    public function insert(array $data)
    {
        $this->_adapter_backend_log->writeLog('insert', $this->_table_name, $data);
        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $this->_adapter_backend_log->writeLog('update', $this->_table_name, $data, $where);
        return parent::update($data, $where);
    }

    public function delete($where)
    {
        $this->_adapter_backend_log->writeLog('delete', $this->_table_name, [], $where);
        return parent::delete($where);
    }
}