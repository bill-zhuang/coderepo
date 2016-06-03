<?php

class Application_Model_DBTable_FinanceCategory extends Application_Model_DBTableFactory
{
    public function __construct()
    {
        parent::__construct('finance_category');
    }

    public function getAllParentCategory($isKeyValueFormat = false)
    {
        $parentData = $this->select()->reset()
            ->from($this->_name, ['fcid', 'name'])
            ->where('parent_id=?', 0)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->order('weight desc')
            ->query()->fetchAll();
        if ($isKeyValueFormat) {
            $data = [];
            foreach ($parentData as $parentValue) {
                $data[$parentValue['fcid']] = $parentValue['name'];
            }
            return $data;
        }
        return $parentData;
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
        foreach ($data as $value) {
            $names[] = $value['name'];
        }

        return $names;
    }

    public function getFinanceSubcategory($parentId)
    {
        $subcategoryData = $this->select()->reset()
            ->from($this->_name, ['fcid', 'name'])
            ->where('parent_id=?', $parentId)
            ->where('status=?', Bill_Constant::VALID_STATUS)
            ->query()->fetchAll();
        $data = [];
        foreach ($subcategoryData as $subcategoryValue) {
            $data[$subcategoryValue['fcid']] = $subcategoryValue['name'];
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