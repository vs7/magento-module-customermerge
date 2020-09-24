<?php

class VS7_CustomerMerge_Block_Adminhtml_Customer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_customer';
        parent::__construct();

        $this->_blockGroup = 'vs7_customermerge';
        $this->_mode = 'edit';

        $this->_removeButton('delete');
        $this->_removeButton('save');
        $this->_removeButton('reset');

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";

        $model = Mage::registry('current_customer');

        $this->_headerText = $model->getEmail() ? $model->getEmail() : $this->__('Customer Edit');
    }
}