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

    public function getFinanceCategoryByID($fcid)
    {
        return $this->select()->reset()
            ->where('fcid=?', $fcid)
            ->query()->fetch();
    }

    public function getAllParentCategory($is_key_value_format = false)
    {
        $parent_data = $this->select()->reset()
            ->from($this->_name, ['fcid', 'name'])
            ->where('parent_id=?', 0)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->order('weight desc')
            ->query()->fetchAll();
        if ($is_key_value_format)
        {
            $data = [];
            foreach ($parent_data as $parent_value)
            {
                $data[$parent_value['fcid']] = $parent_value['name'];
            }
            return $data;
        }
        return $parent_data;
    }

    public function isFinanceCategoryExist($name, $fcid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'count(*) as total')
            ->where('name=?', $name)
            ->where('fcid!=?', $fcid)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        return $data[0]['total'] == 0 ? false : true;
    }

    public function getFinanceCategoryName($fcid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'name')
            ->where('fcid=?', $fcid)
            ->query()->fetch();
        return isset($data['name']) ? $data['name'] : '';
    }

    public function getFinanceCategoryNames(array $fcids)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'name')
            ->where('fcid in(?)', $fcids)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        $names = [];
        foreach ($data as $value)
        {
            $names[] = $value['name'];
        }

        return $names;
    }

    public function getFinanceSubcategory($parent_id)
    {
        $subcategory_data = $this->select()->reset()
            ->from($this->_name, ['fcid', 'name'])
            ->where('parent_id=?', $parent_id)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        $data = [];
        foreach ($subcategory_data as $subcategory_value)
        {
            $data[$subcategory_value['fcid']] = $subcategory_value['name'];
        }

        return $data;
    }

    public function getFinanceParentCategory($fcid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'parent_id')
            ->where('fcid=?', $fcid)
            ->query()->fetch();
        return isset($data['parent_id']) ? $data['parent_id'] : 0;
    }

    public function getParentCategoryName($fcid)
    {
        $data = $this->select()->reset()
            ->from($this->_name, 'name')
            ->where('fcid=?', $fcid)
            ->query()->fetch();
        return isset($data['name']) ? $data['name'] : '';
    }
}