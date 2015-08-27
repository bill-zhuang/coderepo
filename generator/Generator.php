<?php

namespace generator;

class TemplateGenerator
{
    private $_module_name;
    private $_controller_name;
    private $_page_title;
    private $_all_batch_id;
    private $_batch_id;
    private $_table_row_data;
    private $_primary_id;
    private $_model_names;
    private $_table_names;
    private $_table_prefix;
    private $_view_modal_size;
    private $_is_blacklist;
    private $_is_ckeditor;
    private $_is_datetime_picker;

    private $_cache_table_info;

    public function __construct()
    {
        //module
        $this->_module_name = 'person';
        //controller
        $this->_controller_name = 'FinanceCategory'; //camel case
        //view
        $this->_page_title = 'FinanceCategory';
        $this->_all_batch_id = '';
        $this->_batch_id = '';
        $this->_table_row_data = [
            'name' => 'fc_name',
            'weight' => 'fc_weight',
            'create time' => 'fc_create_time',
            'update time' => 'fc_update_time',
        ];
        $this->_view_modal_size = 'md'; //optional: sm/md/lg, refer to small/middle/large
        $this->_is_blacklist = false;
        $this->_is_ckeditor = false;
        $this->_is_datetime_picker = false;
        //tables
        $this->_table_names = [
            'finance_category',
        ];
        $this->_table_prefix = ''; //like bill_
        if (!empty($this->_table_names))
        {
            $this->_primary_id = $this->_getTablePrimaryID($this->_table_names[0]);
            //models
            $this->_model_names = array_map([$this, '_getModelNameByTableName'], $this->_table_names);
        }
        else
        {
            $this->_primary_id = '';
            $this->_model_names = [];
        }

        //cache
        $this->_cache_table_info = [];
    }

    public function generate()
    {
        $this->_generateModuleDirectory();
        $this->_generateModelFile();
        $this->_generateControllerFile();
        $this->_generateViewFile();
    }

    private function _generateModuleDirectory()
    {
        if ($this->_module_name !== '')
        {
            //create folder is not exist
            $module_path = __DIR__ . '/../application/modules/' . strtolower($this->_module_name);
            $create_folder_path = [
                $module_path,
                $module_path . '/controllers',
                $module_path . '/models',
                $module_path . '/views',
            ];
            foreach ($create_folder_path as $dir_path)
            {
                $this->_createDirectory($dir_path);
            }
        }
    }

    private function _generateViewFile()
    {
        $camel_name = $this->_splitControllerName();
        $folder_name = strtolower(implode('-', $camel_name));
        if ($folder_name !== '')
        {
            $view_folder_path = __DIR__ . '/../application/'
                . ($this->_module_name === '' ? '' : ('modules/' . strtolower($this->_module_name) . '/'))
                . 'views/scripts/' . $folder_name;
            $this->_createDirectory($view_folder_path);
            //create view file
            $template_path = __DIR__ . '/template/ViewTemplate.php';
            $dest_path = $view_folder_path . '/index.phtml';
            $params = [
                'module_name' => $this->_module_name,
                'controller_name' => $folder_name,
                'page_title_name' => $this->_page_title,
                'all_batch_id' => $this->_all_batch_id,
                'batch_id' => $this->_batch_id,
                'table_row_data' => $this->_table_row_data,
                'primary_id' => $this->_primary_id,
                'table_data' => empty($this->_table_names) ? [] : $this->_getTableInsertArrayForController($this->_table_names[0]),
                'form_element_prefix' => strtolower(implode('_', $camel_name)),
                'form_name_postfix' => implode('', $camel_name),
                'view_modal_size' => $this->_view_modal_size,
                'is_blacklist' => $this->_is_blacklist,
                'is_ckeditor' => $this->_is_ckeditor,
                'is_datetime_picker' => $this->_is_datetime_picker,
            ];
            $create_result = $this->_renderFile($template_path, $dest_path, $params);
            if ($create_result !== false)
            {
                echo 'Create View File index.phtml Successfully' . PHP_EOL;
            }
            else
            {
                echo 'Create View File index.phtml Failed' . PHP_EOL;
            }
        }
    }

    private function _generateModelFile()
    {
        $model_folder_path = __DIR__ . '/../application/models/DBTable/';
        $this->_createDirectory($model_folder_path);
        //create table model
        foreach ($this->_table_names as $table_name)
        {
            $model_name = $this->_getModelNameByTableName($table_name);

            $params = [
                'model_name' => $model_name,
                'table_name' => $table_name,
                'primary_id' => $this->_getTablePrimaryID($table_name),
                'controller_name' => $this->_controller_name,
            ];
            $template_path = __DIR__ . '/template/ModelTemplate.php';
            $dest_path = $model_folder_path . '/' . $model_name . '.php';

            $create_result = $this->_renderFile($template_path, $dest_path, $params);
            if ($create_result !== false)
            {
                echo 'Create Model File ' . $model_name . '.php Successfully' . PHP_EOL;
            }
            else
            {
                echo 'Create Model File ' . $model_name . '.php Failed' . PHP_EOL;
            }
        }
    }

    private function _generateControllerFile()
    {
        if ($this->_controller_name === '')
        {
            echo 'Controller Name can\'t be empty.' . PHP_EOL;
            exit;
        }
        $controller_folder_path = __DIR__ . '/../application/'
            . ($this->_module_name === '' ? '' : ('modules/' . strtolower($this->_module_name) . '/'))
            . 'controllers';
        $this->_createDirectory($controller_folder_path);
        //create controller
        $params = [
            'module_name' => $this->_module_name,
            'model_names' => $this->_model_names,
            'table_prefix' => $this->_table_prefix,
            'table_names' => $this->_table_names,
            'controller_name' => $this->_controller_name,
            'primary_id' => $this->_primary_id,
            'table_data' => empty($this->_table_names) ? [] : $this->_getTableInsertArrayForController($this->_table_names[0]),
            'form_element_prefix' => strtolower(implode('_', $this->_splitControllerName())),
        ];
        $template_path = __DIR__ . '/template/ControllerTemplate.php';
        $dest_path = $controller_folder_path . '/' . $this->_controller_name . 'Controller.php';

        $create_result = $this->_renderFile($template_path, $dest_path, $params);
        if ($create_result !== false)
        {
            echo 'Create Controller File ' . $this->_controller_name . '.php Successfully' . PHP_EOL;
        }
        else
        {
            echo 'Create Controller File ' . $this->_controller_name . '.php Failed' . PHP_EOL;
        }
    }

    private function _getModelNameByTableName($table_name)
    {
        $table_name = str_replace($this->_table_prefix, '', $table_name);
        $camel_name = explode('_', $table_name);
        $model_name = implode('', array_map('ucwords', $camel_name));
        return $model_name;
    }

    private function _splitControllerName()
    {
        $preg_camel_word = '/([A-Z][a-z]*)/';
        $is_match = preg_match_all($preg_camel_word, $this->_controller_name, $camel_matches);
        if ($is_match)
        {
            return $camel_matches[1];
        }

        return [];
    }

    //copy & modify from vendor\yiisoft\yii2\db\mysql\Schema.php
    private function _getMySQLFieldType($type)
    {
        $mysql_data_types = $this->_getConfig('mysql_data_type.ini');
        $type_name = '';
        if (preg_match('/^(\w+)(?:\(([^\)]+)\))?/', $type, $matches))
        {
            $type = strtolower($matches[1]);
            if (isset($mysql_data_types[$type]))
            {
                $type_name = $mysql_data_types[$type];
            }

            if (!empty($matches[2]))
            {
                $values = explode(',', $matches[2]);
                if ($type === 'enum')
                {
                    ;
                }
                else
                {
                    $size = intval($values[0]);
                    $update_type = '';
                    if ($size === 1 && $type === 'bit')
                    {
                        $update_type = 'boolean';
                    }
                    elseif ($type === 'bit')
                    {
                        if ($size > 32)
                        {
                            $update_type = 'bigint';
                        }
                        elseif ($size === 32)
                        {
                            $update_type = 'integer';
                        }
                    }
                    if ($update_type !== '')
                    {
                        $type_name = $update_type;
                    }
                }
            }
        }

        return $type_name;
    }

    private function _getTablePrimaryID($table_name)
    {
        $definitions = $this->_getTableInfo($table_name);
        $primary_id = '';
        foreach ($definitions as $definition)
        {
            if ($definition['Key'] === 'PRI')
            {
                $primary_id = $definition['Field'];
            }
        }

        return $primary_id;
    }

    private function _getTableInsertArrayForController($table_name)
    {
        $definitions = $this->_getTableInfo($table_name);
        $table_data = [];
        $element_prefix = strtolower(implode('_', $this->_splitControllerName()));
        foreach ($definitions as $definition)
        {
            //replace fp_payment to finance_payment_payment
            //replace fc_id to finance_payment_fc_id
            if (strpos($definition['Field'], str_replace('_id', '', $this->_primary_id)) !== false)
            {
                $element_name = preg_replace('/^([^_]+)/', $element_prefix, $definition['Field']);
            }
            else
            {
                $element_name = $element_prefix . '_' . $definition['Field'];
            }
            $type = $this->_getMySQLFieldType($definition['Type']);
            switch ($type)
            {
                case 'smallint':
                case 'integer':
                case 'bigint':
                    $field_value = 'intval($_POST[\'' . $element_name .  '\'])';
                    break;
                case 'boolean':
                    $field_value = 'intval($_POST[\'' . $element_name .  '\'])';
                    break;
                case 'float':
                case 'decimal':
                case 'money':
                    $field_value = 'floatval($_POST[\'' . $element_name .  '\'])';
                    break;
                case 'date':
                    $field_value = 'date(\'Y-m-d\')';
                    break;
                case 'time':
                    $field_value = 'date(\'H:i:s\')';
                    break;
                case 'datetime':
                case 'timestamp':
                    $field_value = 'date(\'Y-m-d H:i:s\')';
                    break;
                default: // strings
                    $field_value = 'trim($_POST[\'' . $element_name .  '\'])';;
                    break;
            }
            $table_data[$definition['Field']] = $field_value; //fc_weight => 1
        }

        return $table_data;
    }

    private function _getTableInfo($table_name)
    {
        if (isset($this->_cache_table_info[$table_name]))
        {
            return $this->_cache_table_info[$table_name];
        }

        $db_config = $this->_getConfig('db.ini');
        if ($db_config !== null)
        {
            try
            {
                $adapter = new \PDO(
                    "mysql:host={$db_config['db']['host']};dbname={$db_config['db']['dbname']}",
                    $db_config['db']['username'],
                    $db_config['db']['password'],
                    []
                );
                $sql = 'SHOW FULL COLUMNS FROM ' . $table_name;
                $desc = $adapter->query($sql)->fetchAll();
                //cache & return sql result
                $this->_cache_table_info[$table_name] = $desc;
                return $desc;
            }
            catch (\PDOException $e)
            {
                echo 'Connection failed: ', $e->getMessage(), PHP_EOL;
                exit;
            }
        }
    }

    private function _getConfig($config_name)
    {
        $config_path = __DIR__ . '/configs/' . $config_name;
        if (file_exists($config_path))
        {
            $config = parse_ini_file($config_path, true);
            return $config;
        }

        return null;
    }

    private function _createDirectory($dir_path)
    {
        if (!file_exists($dir_path) && !is_dir($dir_path))
        {
            $ret = mkdir($dir_path, 0777, true);
            if ($ret)
            {
                echo 'Create Directory ' . $dir_path . ' Successfully' . PHP_EOL;
            }
            else
            {
                echo 'Create Directory ' . $dir_path . ' Failed' . PHP_EOL;
            }
        }
    }

    private function _renderFile($template_path, $dest_path, array $params)
    {
        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);
        require($template_path);
        $template_content = ob_get_clean();
        return file_put_contents($dest_path, $template_content);
    }

}

$generator = new TemplateGenerator();
$generator->generate();