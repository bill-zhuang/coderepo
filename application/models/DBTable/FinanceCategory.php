<?php

class Application_Model_DBTable_FinanceCategory extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('finance_category');
    }

    public function getFinanceCategoryCount(array $conditions)
    {
        $select = $this->select()->reset()
            ->from($this->_name, 'count(*) as total');
        foreach ($conditions as $cond => $value)
        {
            $select->where($cond, $value);
        }
        $count = $select->query()->fetchAll();
        return $count[0]['total'];
    }

    public function getFinanceCategoryData(array $conditions, $count, $offset, $order_by)
    {
        $select = $this->select()->reset();
        foreach ($conditions as $cond => $value)
        {
            $select->where($cond, $value);
        }
        $data = $select->limit($count, $offset)->order($order_by)
            ->query()->fetchAll();
        return $data;
    }

    public function getFinanceCategoryByID($fc_id)
    {
        return $this->select()->reset()
            ->where('fc_id=?', $fc_id)
            ->query()->fetch();
    }

    public function getAllParentCategory($is_key_value_format = false)
    {
        $parent_data = $this->select()->reset()
            ->from($this->_name, ['fc_id', 'fc_name'])
            ->where('fc_parent_id=?', 0)
            ->where('fc_status=?', Bill_Constant::VALID_STATUS)
            ->order('fc_weight desc')
            ->query()->fetchAll();
        if ($is_key_value_format)
        {
            $data = [];
            foreach ($parent_data as $parent_value)
            {
                $data[$parent_value['fc_id']] = $parent_value['fc_name'];
            }
            return $data;
        }
        return $parent_data;
    }

    public function isFinanceCategoryExist($name, $fc_id)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('fc_name=?', $name)
            ->where('fc_id!=?', $fc_id)
            ->where('fc_status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        return $data[0]['total'] == 0 ? false : true;
    }

    public function getFinanceCategoryName($fc_id)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'fc_name')
            ->where('fc_id=?', $fc_id)
            ->query()->fetch();
        return isset($data['fc_name']) ? $data['fc_name'] : '';
    }

    public function getFinanceCategoryNames(array $fc_ids)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'fc_name')
            ->where('fc_id in(?)', $fc_ids)
            ->where('fc_status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        $names = [];
        foreach ($data as $value)
        {
            $names[] = $value['fc_name'];
        }

        return $names;
    }

    public function getFinanceSubcategory($parent_id)
    {
        $subcategory_data = $this->select()->reset()
            ->from($this->_name, ['fc_id', 'fc_name'])
            ->where('fc_parent_id=?', $parent_id)
            ->where('fc_status=?', Bill_Constant::VALID_STATUS)
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

    public function getParentCategoryName($fc_id)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'fc_name')
            ->where('fc_id=?', $fc_id)
            ->query()->fetch();
        return isset($data['fc_name']) ? $data['fc_name'] : '';
    }
}