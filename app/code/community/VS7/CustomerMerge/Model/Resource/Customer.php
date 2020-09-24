<?php

class VS7_CustomerMerge_Model_Resource_Customer extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('vs7_customermerge/customer', 'id');
    }
}