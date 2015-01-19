<?php

class Application_Model_DbTable_Financecategory extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('finance_category');
    }

    public function getFinancecategoryCount(array $conditions)
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

    public function getFinancecategoryData(array $conditions, $count, $offset, $order_by)
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

    public function getFinancecategoryByID($fc_id)
    {
        return $this->select()->reset()
            ->where('fc_id=?', $fc_id)
            ->query()->fetch();
    }

    public function getAllParentCategory()
    {
        $parent_data = $this->select()->reset()
            ->from($this->_name, ['fc_id', 'fc_name'])
            ->where('fc_parent_id=?', 0)->where('fc_status=?', 1)
            ->query()->fetchAll();
        $data = [];
        foreach ($parent_data as $parent_value)
        {
            $data[$parent_value['fc_id']] = $parent_value['fc_name'];
        }

        return $data;
    }

    public function isFinanceCategoryExist($name, $fc_id)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('fc_name=?', $name)->where('fc_id!=?', $fc_id)->where('fc_status=?', 1)
            ->query()->fetchAll();
        return $data[0]['total'] == 0 ? false : true;
    }

    public function getFinaceCategoryName($fc_id)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'fc_name')
            ->where('fc_id=?', $fc_id)
            ->query()->fetch();
        return isset($data['fc_name']) ? $data['fc_name'] : '';
    }

    public function getFinanceSubcategory($parent_id)
    {
        $subcategory_data = $this->select()->reset()
            ->from($this->_name, ['fc_id', 'fc_name'])
            ->where('fc_parent_id=?', $parent_id)->where('fc_status=?', 1)
            ->query()->fetchAll();
        $data = [];
        foreach ($subcategory_data as $subcategory_value)
        {
            $data[$subcategory_value['fc_id']] = $subcategory_value['fc_name'];
        }

        return $data;
    }

    public function getFinanceParentCategory($fc_id)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'fc_parent_id')
            ->where('fc_id=?', $fc_id)
            ->query()->fetch();
        return isset($data['fc_parent_id']) ? $data['fc_parent_id'] : 0;
    }
}