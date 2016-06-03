<?php
/* @var $model_name string model name */
/* @var $table_name string table name */
/* @var $primary_id array table primary id */

echo "<?php\n";
?>

class Application_Model_DBTable_<?php echo $model_name; ?> extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('<?php echo $table_name; ?>');
    }
}