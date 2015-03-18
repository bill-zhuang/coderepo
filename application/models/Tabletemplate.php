<?php

class Application_Model_DBTable_{class_name} extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('{table_name}');
    }

    public function get{class_name}Count(array $conditions)
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

    public function get{class_name}Data(array $conditions, $count, $offset, $order_by)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $key => $content)
        {
            $select->where($key . ' ' . $content['compare_type'], $content['value']);
        }
        $data = $select->limit($count, $offset)->order($order_by)
            ->query()->fetchAll();
        return $data;
    }

    public function get{class_name}ByID(${pkid})
    {
        return $this->select()->reset()
            ->where('{pkid}=?', ${pkid})
            ->query()->fetch();
    }
}