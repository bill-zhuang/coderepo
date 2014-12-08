<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-12-8
 * Time: 下午1:30
 */

class Application_Models_DBTableFactory extends Zend_Db_Table_Abstract
{
    public function __construct($table_name, $section_name = 'localdb')
    {
        $config = [
            'db' => self::_getDBAdapter($section_name),
            'name' => $table_name
        ];
        parent::__construct($config);
    }

    /**
     * @param string $section_name in db.ini file
     * @return mixed|Zend_Db_Adapter_Abstract
     */
    private static function _getDBAdapter($section_name = 'localdb')
    {
        if (!Zend_Registry::isRegistered($section_name))
        {
            $db_config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/db.ini', $section_name);print_r($db_config);
            if ($db_config->adapter == '')
            {
                $db_config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/db.ini', 'localdb');
            }

            $db_adapter = Zend_Db::factory($db_config->adapter, $db_config->database->toArray());
            Zend_Registry::set($section_name, $db_adapter);
            return $db_adapter;
        }
        else
        {
            return Zend_Registry::get($section_name);
        }
    }
}