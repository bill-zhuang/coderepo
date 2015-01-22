<?php

class AutomationController extends Zend_Controller_Action
{
    public $module_name;
    public $used_tables;
    public $controller_name;
    public $action_name;
    public $view_title;
    public $view_name;
    public $primary_key;
    public $all_batch_id;
    public $batch_id;
    public $table_prefix;
    
    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout()->disableLayout();
        $this->module_name = 'person';//modules name here
        $this->used_tables = ['finance_category'];//table name here
        $this->controller_name = 'financecategory';//controller name here
        $this->action_name = 'Financecategory';//first letter big, remove prefix of gc_;
        $this->view->title = '消费分类管理';
        $this->view_name = strtolower($this->controller_name);
        $this->primary_key = 'fc_id';
        $this->all_batch_id = 'all_comments'; //option
        $this->batch_id = 'comment'; //option
        $this->table_prefix = substr($this->primary_key, 0, strlen($this->primary_key) - 3);
    }

    public function indexAction()
    {
        // action body
        exit;
    }

    public function gentablemodelAction()
    {
        $table_names = ['finance_category'];//table name here
        foreach ($table_names as $table_name)
        {
            $new_file_name = '';
            $pkid = '';
            $split_names = explode('_', $table_name);
            foreach ($split_names as $key => $name)
            {
                $new_file_name .= $name;
                $pkid .= $name[0];
            }
            $pkid .= '_id';
            $new_file_name[0] = strtoupper($new_file_name[0]);

            $template_file_path = APPLICATION_PATH . '/models/Tabletemplate.php';
            $new_file_path = APPLICATION_PATH . '/models/DBTable/' . $new_file_name . '.php';

            if (!file_exists($new_file_path))
            {
                $copy_flag = copy($template_file_path, $new_file_path);
                if ($copy_flag === true)
                {
                    $content = file_get_contents($new_file_path);
                    $write_flag = file_put_contents(
                        $new_file_path,
                        str_replace(
                            ['{class_name}', '{table_name}', '{pkid}', '{table_prefix}'],
                            [$new_file_name, $table_name, $pkid, $this->table_prefix],
                            $content
                        )
                    );
                    if ($write_flag !== false)
                    {
                        echo 'table model ' . $new_file_name . '.php created successfully.' . "<br/>";
                    }
                    else
                    {
                        echo 'replace file content failed.' . "<br/>";
                    }
                }
                else
                {
                    echo 'copy file failed.' . "<br/>";
                }
            }
            else
            {
                echo 'table model ' . $new_file_name . '.php already existed.' . "<br/>";
            }
        }

        exit;
    }
    
    public function gencontrollerAction()
    {
        $module_name = $this->module_name;//modules name here
        $controller_name = $this->controller_name;//controller name here
        $action_name = $this->action_name;//action name here
        
        $controller_name = strtolower($controller_name);
        $view_path = APPLICATION_PATH . '/modules/' . $module_name .'/views/scripts/' . $controller_name;
        $this->_createFolder($view_path);
        
        $controller_name[0] = strtoupper($controller_name[0]);
        
        $template_file_path = APPLICATION_PATH . '/controllers/Templatecontroller.php';
        $new_file_path = APPLICATION_PATH . '/modules/' . $module_name .'/controllers/' . $controller_name . 'Controller.php';
        if (!file_exists($new_file_path))
        {
            $copy_flag = copy($template_file_path, $new_file_path);
            if ($copy_flag === true)
            {
                $table_array = $this->used_tables;//table name here

                $content = file_get_contents($new_file_path);
                $replace_array = ['search' => [], 'replace' => []];
                $replace_array['search'] = ['TemplateController',
                    '{module_name}',
                    '{action_object_name}',
                    '{action_object_name_first_big_letter}',
                    '{main}',
                    '{primary_key}',
                    '{table_prefix}',
                ];
                $replace_array['replace'] = [$controller_name . 'Controller',
                    $module_name,
                    strtolower($action_name), //action object name here
                    $action_name, //first letter big
                    $table_array[0], //main select, first table array value
                    $this->primary_key, //table key
                    $this->table_prefix,
                ];
                file_put_contents($new_file_path, str_replace($replace_array['search'], $replace_array['replace'], $content));

                //
                //$require_line = 1;
                $adapter_line = 4;
                $init_adapter_line = 9;
                $content_lines = file($new_file_path, FILE_IGNORE_NEW_LINES);
                foreach ($table_array as $table_name)
                {
                    $adapter_name = str_replace('bill_', '', $table_name);
                    $require_name = str_replace('_', '', $adapter_name);
                    $require_name[0] = strtoupper($require_name[0]);
                    $table_name = $require_name;
                    //$require_content = 'require_once APPLICATION_PATH' . ' . \'/models/DBTable/' . $require_name . '.php\';';
                    $adapter_content = "\t" . 'private $_adapter_' . $adapter_name . ';';
                    $init_adapter_content = "\t\t" . '$this->_adapter_' . $adapter_name . ' = new Application_Model_DBTable_' . $table_name . '();';

                    //$content_lines[$require_line] = $require_content . "\n" . $content_lines[$require_line];
                    $content_lines[$adapter_line] = $adapter_content . "\n" . $content_lines[$adapter_line];
                    $content_lines[$init_adapter_line] = $init_adapter_content . "\n" . $content_lines[$init_adapter_line];
                }

                $write_flag = file_put_contents($new_file_path, implode("\n", $content_lines));
                if ($write_flag !== false)
                {
                    //genereate phpdoc for table adapter (run this when IDE is phpstorm!!!, zend studio no needed)
                    $generate_phpdoc = new generatePhpDoc();
                    $generate_phpdoc->run();

                    echo 'controller ' . $controller_name . 'Controller.php created successfully.';
                }
                else
                {
                    echo 'replace file content failed.';
                }
            }
            else
            {
                echo 'copy file failed.';
            }
        }
        else
        {
            echo 'file already existed.';
        }
        
        exit;
    }
    
    public function genviewAction()
    {
        $module_name = $this->module_name;//modules name here
        $controller_name = $this->controller_name;//controller name here
        $view_name = $this->view_name;//view name here
        
        $view_name = strtolower($view_name);
        $view_path = APPLICATION_PATH . '/modules/' . $module_name .'/views/scripts/' . $view_name . '/';
        $this->_createFolder($view_path);
        
        $template_file_path = APPLICATION_PATH . '/views/scripts/template/index.phtml';
        $new_file_path = APPLICATION_PATH . '/modules/' . $module_name .'/views/scripts/' . $view_name . '/index.phtml';
        if (!file_exists($new_file_path))
        {
            $copy_flag = copy($template_file_path, $new_file_path);
            if ($copy_flag === true)
            {
                $content = file_get_contents($new_file_path);
                $replace_array = ['search' => [], 'replace' => []];
                $replace_array['search'] = [
                    'TemplateController',
                    '{module_name}',
                    '{controller_name}',
                    '{type_name}',
                    '{type_name_low_case}',
                    '{primary_key}',
                    '{page_title}',
                    '{all_batch_id}', //option
                    '{batch_id}', //option
                    '{table_prefix}',
                ];
                $replace_array['replace'] = [
                    $controller_name . 'Controller',
                    $module_name,
                    strtolower($controller_name),
                    $this->action_name, //first letter big
                    strtolower($this->action_name), //all lower case
                    $this->primary_key, //table key
                    $this->view->title,
                    $this->all_batch_id,
                    $this->batch_id,
                    $this->table_prefix,
                ];
                $write_flag = file_put_contents($new_file_path, str_replace($replace_array['search'], $replace_array['replace'], $content));
                if ($write_flag !== false)
                {
                    echo 'view ' . $view_name . '.phtml created successfully.';
                }
                else
                {
                    echo 'replace file content failed.';
                }
            }
            else
            {
                echo 'copy file failed.';
            }
        }
        else
        {
        	echo 'file already existed.';
        }
        
        exit;
    }
    
    private function _createFolder($dir)
    {
        if (!file_exists($dir))
        {
        	mkdir ($dir, 0777, true);
        }
    }
}

class generatePhpDoc
{
    private $_path;

    public function __construct()
    {
        $this->_path = [
            APPLICATION_PATH . '/modules/person/controllers',
            //APPLICATION_PATH . '/modules/person/models'
        ];
    }

    public function run()
    {
        $preg = '/((?:private|public)\s+\$([^;]+);)/';
        $preg_instance = '/\$this\->%s\s+=\s+new\s+([^\(]+)\(/';
        $preg_instance_exist = '/@var\s+%s/';
        foreach ($this->_path as $path)
        {
            if ($handle = opendir($path))
            {
                while (false !== ($file = readdir($handle)))
                {
                    if ($file == '.' || $file == '..')
                    {
                        continue;
                    }
                    else
                    {
                        $find = [];
                        $replace = [];

                        $absolute_path = $path . '\\' . $file;
                        /*echo $full_path;
                        exit;*/
                        $content = file_get_contents($absolute_path);
                        $new_content = $content;
                        if ($content !== false)
                        {
                            $is_match_init = preg_match_all($preg, $content, $init_matches);
                            /*print_r($init_matches);
                            exit;*/
                            if ($is_match_init > 0)
                            {
                                foreach ($init_matches[2] as $key => $init_val)
                                {
                                    $preg_instance_new = sprintf($preg_instance, $init_val);
                                    /*echo $preg_instance_new;
                                    exit;*/
                                    $is_match_instance = preg_match_all($preg_instance_new, $content, $instance_matches);
                                    /*var_dump($is_match_instance);
                                    exit;*/
                                    if ($is_match_instance > 0)
                                    {
                                        /*print_r($preg_instance);
                                        exit;*/
                                        foreach ($instance_matches[1] as $instance_val)
                                        {
                                            $preg_instance_exist_new = sprintf($preg_instance_exist, $instance_val);
                                            $is_match_exist_instance = preg_match($preg_instance_exist_new, $content);
                                            if ($is_match_exist_instance == 0)
                                            {
                                                $find[] = $init_matches[1][$key];
                                                $replace[] = "/**\r\n\t * @var " . $instance_val .
                                                    "\r\n\t */\r\n\t" . $init_matches[1][$key];
                                                break;
                                            }
                                        }
                                    }
                                }

                                $content = str_replace($find, $replace, $content);
                                file_put_contents($absolute_path, $content);
                            }
                        }
                    }
                }
            }
        }
    }
}

