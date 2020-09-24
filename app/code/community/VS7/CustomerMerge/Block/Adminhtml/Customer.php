<?php

class VS7_CustomerMerge_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_customer';
        $this->_headerText = Mage::helper('vs7_customermerge')->__('VS7 Customer Merge - Customers List');
        $this->_blockGroup = 'vs7_customermerge';

        parent::__construct();

        $this->_removeButton('add');
    }
}