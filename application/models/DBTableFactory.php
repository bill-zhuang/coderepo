<?php
/**
 * Created by bill-zhuang.
 * User: bill-zhuang
 * Date: 14-12-8
 * Time: 下午1:30
 */

class Application_Model_DBTableFactory extends Zend_Db_Table_Abstract
{
    private $_tableName;
    private $_adapterBackendLog;

    public function __construct($tableName, $sectionName = Bill_Constant::LOCAL_DB)
    {
        $this->_tableName = $tableName;
        $config = [
            Zend_Db_Table_Abstract::ADAPTER => Application_Model_DBAdapter::getDBAdapter($sectionName),
            Zend_Db_Table_Abstract::NAME => $this->_tableName,
        ];
        parent::__construct($config);
        $this->_adapterBackendLog = new Application_Model_DBTable_BackendLog();
    }

    public function insert(array $data)
    {
        $this->_adapterBackendLog->writeLog('insert', $this->_tableName, $data);
        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        $this->_adapterBackendLog->writeLog('update', $this->_tableName, $data, $where);
        return parent::update($data, $where);
    }

    public function delete($where)
    {
        $this->_adapterBackendLog->writeLog('delete', $this->_tableName, [], $where);
        return parent::delete($where);
    }
}