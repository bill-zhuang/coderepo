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
        if (!Zend_Registry::isRegistered($section_name))
        {
            $db_config_path = APPLICATION_PATH . '/configs/db.ini';
            $db_config = new Zend_Config_Ini($db_config_path, $section_name);
            if (isset($db_config->adapter) && isset($db_config->database))
            {
                $db_adapter = Zend_Db::factory($db_config->adapter, $db_config->database->toArray());
                Zend_Registry::set($section_name, $db_adapter);
                return $db_adapter;
            }
            else
            {
                throw new Exception('Database config error!');
            }
        }
        else
        {
            return Zend_Registry::get($section_name);
        }
    }
}