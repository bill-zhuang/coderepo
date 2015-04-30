<?php
/* @var $model_name string model name */
/* @var $table_name string table name */
/* @var $primary_id string table primary id */

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
        foreach ($conditions as $key => $content)
        {
            $select->where($key . ' ' . $content['compare_type'], $content['value']);
        }
        $count = $select->query()->fetchAll();
        return $count[0]['total'];
    }

    public function get<?php echo $model_name; ?>Data(array $conditions, $count, $offset, $order_by)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $key => $content)
        {
            $select->where($key . ' ' . $content['compare_type'], $content['value']);
        }
        $data = $select
            ->limit($count, $offset)
            ->order($order_by)
            ->query()->fetchAll();
        return $data;
    }

    public function get<?php echo $model_name; ?>ByID($<?php echo $primary_id; ?>)
    {
        return $this->select()->reset()
            ->where('<?php echo $primary_id ?>=?', $<?php echo $primary_id ?>)
            ->query()->fetch();
    }
}