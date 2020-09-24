<?php

class VS7_CustomerMerge_Adminhtml_Vs7_CustomermergeController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $customersBlock = $this->getLayout()->createBlock('vs7_customermerge/adminhtml_customer');
        $this->loadLayout()
            ->_title(Mage::helper('vs7_customermerge')->__('VS7 Customer Merge - Customers List'))
            ->_addContent($customersBlock)
            ->renderLayout();
    }

    public function gridAction()
    {
        $customersGridBlock = $this->getLayout()->createBlock('vs7_customermerge/adminhtml_customer_grid');
        $coreTextBlock = $this->getLayout()->createBlock('core/text_list')->append($customersGridBlock);
        $this->getResponse()->setBody($coreTextBlock->toHtml());
    }

    public function editAction()
    {
        $this->_title($this->__('VS7 Customer Merge'))->_title($this->__('Merge'));

        $email = $this->getRequest()->getParam('id');
        $model = Mage::getModel('vs7_customermerge/customer');

        if ($email) {
            $model->load($email, 'email');
            if ($model->getId() == null) {
                $model->setEmail($email);
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('vs7_customermerge')->__('Customer email not set'));
            $this->_redirect('*/*/index');
            return;
        }

        Mage::register('current_customer', $model);

        $customerBlock = $this->getLayout()->createBlock('vs7_customermerge/adminhtml_customer_edit');
        $this->loadLayout()
            ->_addContent($customerBlock)
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $email = $this->getRequest()->getParam('id');
            $model = Mage::getModel('vs7_customermerge/customer')->load($email, 'email');

            $delete = false;
            if (isset($data['delete']) && $data['delete']) {
                $delete = true;
            }

            $canMove = false;
            if ($data['email'] == $model->getEmail() && $model->getPrimaryWebsite() != null) {
                $canMove = true;
            }

            $model->setData($data);

            try {
                if ($delete) {
                    Mage::helper('vs7_customermerge')->delete($data['email'], $model->getPrimaryWebsite());

                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vs7_customermerge')->__('Other customers have been deleted.'));
                } else {
                    $model->save();

                    if ($canMove) {
                        if (isset($data['move_orders']) && $data['move_orders']) {
                            Mage::helper('vs7_customermerge')->moveOrders($data['email'], $model->getPrimaryWebsite());
                        }

                        if (isset($data['move_addresses']) && $data['move_addresses']) {
                            Mage::helper('vs7_customermerge')->moveAddresses($data['email'], $model->getPrimaryWebsite());
                        }

                        if (isset($data['attribute']) && count($data['attribute']) > 0) {
                            Mage::helper('vs7_customermerge')->setAttributes($data['email'], $data['attribute']);
                        }
                    }

                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('vs7_customermerge')->__('Customer has been saved.'));
                }

                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back') && !$delete) {
                    $this->_redirect('*/*/edit', array('id' => $model->getEmail()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
}