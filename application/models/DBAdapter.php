<?php
/**
 * Created by bill-zhuang.
 * User: bill-zhuang
 * Date: 15-11-19
 * Time: 下午4:11
 */

class Application_Model_DBAdapter
{
    /**
     * @param string $sectionName in db.ini file
     * @return mixed|Zend_Db_Adapter_Abstract
     * @throws Exception when no db config exist
     */
    public static function getDBAdapter($sectionName = Bill_Constant::LOCAL_DB)
    {
        if (Zend_Registry::isRegistered($sectionName)) {
            return Zend_Registry::get($sectionName);
        } else {
            throw new Exception('Database config error!');
        }
    }
}