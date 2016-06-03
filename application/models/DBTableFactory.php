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

    public function getSearchData(array $conditions, $startPage, $pageLength, $orderBy, $groupBy = null)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $select
            ->limitPage($startPage, $pageLength)
            ->order($orderBy);
        if ($groupBy !== null) {
            $select->group($groupBy);
        }
        $data = $select
            ->query()->fetchAll();
        return $data;
    }

    public function getSearchCount(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'count(*) as total');
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return $count[0]['total'];
    }

    public function getByPrimaryKey()
    {
        $primaryKey = func_get_args();
        $rowSets = $this->find($primaryKey);
        if ($rowSets->count() > 0) {
            return $rowSets->current()->toArray();
        } else {
            return array();
        }
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