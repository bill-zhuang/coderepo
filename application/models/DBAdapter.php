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
     * @param string $section_name in db.ini file
     * @return mixed|Zend_Db_Adapter_Abstract
     * @throws Exception when no db config exist
     */
    public static function getDBAdapter($section_name = 'localdb')
    {
        if (Zend_Registry::isRegistered($section_name))
        {
            return Zend_Registry::get($section_name);
        }
        else
        {
            throw new Exception('Database config error!');
        }
    }
}