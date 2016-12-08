<?php

class TemplateGenerator
{
    private $_configs;
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
    private $_using_ckeditor;
    private $_using_datetime_picker;
    private $_tab_types;
    private $_default_tab_value;

    private $_cache_table_info;

    public function __construct()
    {
        $configs = $this->_getConfig('config.ini');
        if ($configs !== null) {
            $this->_configs = $configs;
            //table
            $this->_table_names = $configs['table']['tableNames'];
            $this->_table_prefix = $configs['table']['tablePrefix'];
            //module
            $this->_module_name = $configs['module']['moduleName'];
            //controller
            $this->_controller_name = $configs['controller']['controllerName'];
            //view
            $this->_page_title = $configs['view']['pageTitle'];
            $this->_table_row_data = $configs['view']['tableColumns'];
            $this->_all_batch_id = $configs['view']['allCheckboxID'];
            $this->_batch_id = $configs['view']['checkboxID'];
            $this->_view_modal_size = $configs['view']['modalSize'];
            $this->_using_ckeditor = boolval($configs['view']['usingCKEditor']);
            $this->_using_datetime_picker = boolval($configs['view']['usingDatetimePicker']);
            $this->_tab_types = array_filter($configs['view']['tabOptions']);
            $this->_default_tab_value = $configs['view']['defaultTabValue'];
        } else {
            echo 'config.ini not exist.', PHP_EOL;
            exit;
        }
        if (!empty($this->_table_names)) {
            $this->_primary_id = $this->_getTablePrimaryID($this->_table_names[0]);
            //models
            $this->_model_names = array_map([$this, '_getModelNameByTableName'], $this->_table_names);
        } else {
            $this->_primary_id = [];
            $this->_model_names = [];
        }

        //cache
        $this->_cache_table_info = [];
    }

    public function generate()
    {
        $this->_generateModuleDirectory();
        $this->_generateModelFile();
        //$this->_generateModelObjectFile();
        $this->_generateControllerFile();
        $this->_generateViewFile();
        $this->_generateJsFile();
    }

    private function _generateModuleDirectory()
    {
        if ($this->_module_name !== '') {
            //create folder is not exist
            $module_path = __DIR__ . '/../application/modules/' . strtolower($this->_module_name);
            $create_folder_path = [
                $module_path,
                $module_path . '/controllers',
                $module_path . '/models',
                $module_path . '/views',
            ];
            foreach ($create_folder_path as $dir_path) {
                $this->_createDirectory($dir_path);
            }
        }
    }

    private function _generateViewFile()
    {
        $camel_name = $this->_splitControllerName();
        $folder_name = strtolower(implode('-', $camel_name));
        if ($folder_name !== '') {
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
                'using_ckeditor' => $this->_using_ckeditor,
                'using_datetime_picker' => $this->_using_datetime_picker,
                'tab_types' => $this->_tab_types,
                'default_tab_value' => $this->_default_tab_value,
            ];
            $create_result = $this->_renderFile($template_path, $dest_path, $params);
            if ($create_result !== false) {
                echo 'Create View File index.phtml Successfully' . PHP_EOL;
            } else {
                echo 'Create View File index.phtml Failed' . PHP_EOL;
            }
        }
    }

    private function _generateJsFile()
    {
        $camel_name = $this->_splitControllerName();
        $folder_name = strtolower(implode('-', $camel_name));
        if ($folder_name !== '') {
            $js_folder_name = $this->_module_name === '' ? 'default' : strtolower($this->_module_name);
            $js_folder_path = __DIR__ . '/../public/js/' . $js_folder_name;
            $this->_createDirectory($js_folder_path);
            //create js file
            $template_path = __DIR__ . '/template/JsTemplate.php';
            $dest_path = $js_folder_path . '/' . $folder_name . '.js';
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
                'using_ckeditor' => $this->_using_ckeditor,
                'tab_types' => $this->_tab_types,
                'default_tab_value' => $this->_default_tab_value,
            ];
            $create_result = $this->_renderFile($template_path, $dest_path, $params);
            if ($create_result !== false) {
                echo 'Create Js File ' . $folder_name . '.js Successfully' . PHP_EOL;
            } else {
                echo 'Create Js File ' . $folder_name . '.js Failed' . PHP_EOL;
            }
        }
    }

    private function _generateModelFile()
    {
        $model_folder_path = __DIR__ . '/../application/models/DBTable/';
        $this->_createDirectory($model_folder_path);
        //create table model
        foreach ($this->_table_names as $table_name) {
            $model_name = $this->_getModelNameByTableName($table_name);

            $params = [
                'model_name' => $model_name,
                'table_name' => $table_name,
                'primary_id' => $this->_getTablePrimaryID($table_name),
                'controller_name' => $this->_controller_name,
            ];
            $template_path = __DIR__ . '/template/ModelTemplate.php';
            $dest_path = $model_folder_path . '/' . $model_name . '.php';

            if (!file_exists($dest_path)) {
                $create_result = $this->_renderFile($template_path, $dest_path, $params);
                if ($create_result !== false) {
                    echo 'Create Model File ' . $model_name . '.php Successfully' . PHP_EOL;
                } else {
                    echo 'Create Model File ' . $model_name . '.php Failed' . PHP_EOL;
                }
            }
        }
    }

    private function _generateModelObjectFile()
    {
        $model_folder_path = __DIR__ . '/../application/models/DBObject/';
        $this->_createDirectory($model_folder_path);
        //create table model
        foreach ($this->_table_names as $table_name) {
            $model_name = $this->_getModelNameByTableName($table_name);

            $params = [
                'model_name' => $model_name,
                'table_name' => $table_name,
                'primary_id' => $this->_getTablePrimaryID($table_name),
                'table_fields' => $this->_getTableInfo($table_name),
            ];
            $template_path = __DIR__ . '/template/ModelObjectTemplate.php';
            $dest_path = $model_folder_path . '/' . $model_name . '.php';

            $create_result = $this->_renderFile($template_path, $dest_path, $params);
            if ($create_result !== false) {
                echo 'Create Model Object File ' . $model_name . '.php Successfully' . PHP_EOL;
            } else {
                echo 'Create Model Object File ' . $model_name . '.php Failed' . PHP_EOL;
            }
        }
    }

    private function _generateControllerFile()
    {
        if ($this->_controller_name === '') {
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
            'tab_types' => $this->_tab_types,
            'default_tab_value' => $this->_default_tab_value,
            'using_datetime_picker' => $this->_using_datetime_picker,
        ];
        $template_path = __DIR__ . '/template/ControllerTemplate.php';
        $dest_path = $controller_folder_path . '/' . $this->_controller_name . 'Controller.php';

        $create_result = $this->_renderFile($template_path, $dest_path, $params);
        if ($create_result !== false) {
            echo 'Create Controller File ' . $this->_controller_name . '.php Successfully' . PHP_EOL;
        } else {
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
        if ($is_match) {
            return $camel_matches[1];
        }

        return [];
    }

    //copy & modify from vendor\yiisoft\yii2\db\mysql\Schema.php
    private function _getMySQLFieldType($type)
    {
        $mysql_data_types = $this->_getConfig('mysql_data_type.ini');
        $type_name = '';
        if (preg_match('/^(\w+)(?:\(([^\)]+)\))?/', $type, $matches)) {
            $type = strtolower($matches[1]);
            if (isset($mysql_data_types[$type])) {
                $type_name = $mysql_data_types[$type];
            }

            if (!empty($matches[2])) {
                $values = explode(',', $matches[2]);
                if ($type === 'enum') {
                    ;
                } else {
                    $size = intval($values[0]);
                    $update_type = '';
                    if ($size === 1 && $type === 'bit') {
                        $update_type = 'boolean';
                    } else if ($type === 'bit') {
                        if ($size > 32) {
                            $update_type = 'bigint';
                        } else if ($size === 32) {
                            $update_type = 'integer';
                        }
                    }

                    if ($update_type !== '') {
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
        $primary_id = [];
        foreach ($definitions as $definition) {
            if ($definition['Key'] === 'PRI') {
                $primary_id[] = $definition['Field'];
            }
        }

        return $primary_id;
    }

    private function _getTableInsertArrayForController($table_name)
    {
        $definitions = $this->_getTableInfo($table_name);
        $table_data = [];
        $element_prefix = strtolower(implode('_', $this->_splitControllerName()));
        foreach ($definitions as $definition) {
            if (strpos($definition['Field'], str_replace('_id', '', $this->_primary_id[0])) !== false) {
                $element_name = preg_replace('/^([^_]+)/', $element_prefix, $definition['Field']);
            } else {
                $element_name = $element_prefix . '_' . $definition['Field'];
            }
            $type = $this->_getMySQLFieldType($definition['Type']);
            switch ($type) {
                case 'smallint':
                case 'integer':
                case 'bigint':
                    $field_value = 'intval($params[\'' . $element_name .  '\'])';
                    break;
                case 'boolean':
                    $field_value = 'intval($params[\'' . $element_name .  '\'])';
                    break;
                case 'float':
                case 'decimal':
                case 'money':
                    $field_value = 'floatval($params[\'' . $element_name .  '\'])';
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
                    $field_value = 'trim($params[\'' . $element_name .  '\'])';;
                    break;
            }
            $table_data[$definition['Field']] = $field_value; //fc_weight => 1
        }

        return $table_data;
    }

    private function _getTableInfo($table_name)
    {
        if (isset($this->_cache_table_info[$table_name])) {
            return $this->_cache_table_info[$table_name];
        }

        $db_config = $this->_configs['db'];
        if ($db_config !== null) {
            try {
                $adapter = new \PDO(
                    "mysql:host={$db_config['host']};dbname={$db_config['dbname']}",
                    $db_config['username'],
                    $db_config['password'],
                    []
                );
                $sql = 'SHOW FULL COLUMNS FROM ' . $table_name;
                $desc = $adapter->query($sql)->fetchAll();
                //cache & return sql result
                $this->_cache_table_info[$table_name] = $desc;
                return $desc;
            } catch (\PDOException $e) {
                echo 'Connection failed: ', $e->getMessage(), PHP_EOL;
                exit;
            }
        }
    }

    private function _getConfig($config_name)
    {
        $config_path = __DIR__ . '/configs/' . $config_name;
        if (file_exists($config_path)) {
            $config = parse_ini_file($config_path, true);
            return $config;
        }

        return null;
    }

    private function _createDirectory($dir_path)
    {
        if (!file_exists($dir_path) && !is_dir($dir_path)) {
            $ret = mkdir($dir_path, 0777, true);
            if ($ret) {
                echo 'Create Directory ' . $dir_path . ' Successfully' . PHP_EOL;
            } else {
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