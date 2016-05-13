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

    public function get<?php echo $model_name; ?>Count(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'count(*) as total');
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return intval($count[0]['total']);
    }

    public function get<?php echo $model_name; ?>Data(array $conditions, $startPage, $pageLength, $orderBy)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $cond => $value) {
            $select->where($cond, $value);
        }
        $data = $select
            ->order($orderBy)
            ->limitPage($startPage, $pageLength)
            ->query()->fetchAll();
        return $data;
    }

    public function get<?php echo $model_name; ?>ByID($<?php echo $primary_id[0]; ?>)
    {
        return $this->select()->reset()
            ->where('<?php echo $primary_id[0] ?>=?', $<?php echo $primary_id[0] ?>)
            ->query()->fetch();
    }
}