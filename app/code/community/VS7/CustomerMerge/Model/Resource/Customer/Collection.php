<?php

class VS7_CustomerMerge_Model_Resource_Customer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('vs7_customermerge/customer');
    }

    protected function _initSelect()
    {
        $customerTableName = Mage::getSingleton('core/resource')->getTableName('customer_entity');
        $subQuery = '(SELECT `email`, COUNT(*) AS count FROM `' . $customerTableName . '` GROUP BY `email` HAVING count > 1)';
        $subQuery = new Zend_Db_Expr($subQuery);
        $this->getSelect()
            ->from(array('e' => $this->getMainTable()))
            ->joinRight(
                $subQuery,
                't.email = e.email'
            )
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array('t.email', 'e.primary_website'));
        ;
        return $this;
    }
}