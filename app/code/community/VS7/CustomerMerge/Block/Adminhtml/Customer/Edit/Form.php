<?php

class VS7_CustomerMerge_Block_Adminhtml_Customer_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_customer');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $form->setHtmlIdPrefix('vs7_customermerge_');
        $form->setUseContainer(true);
        $this->setForm($form);

        if ($model->getEmail()) {
            $helper = Mage::helper('vs7_customermerge');
            $stats = $helper->getStats($model->getEmail());

            $fieldset = $form->addFieldset('website', array('legend' => $helper->__('Set Primary Website')));
            $fieldset->addType('website', 'VS7_CustomerMerge_Block_Adminhtml_Customer_Edit_Form_Renderer_Fieldset_Website');

            $fieldset->addField('email', 'hidden', array(
                'name' => 'email',
            ));

            $fieldset->addField('primary_website', 'website', array(
//                'label' => $helper->__('Primary Website Id'),
                'name' => 'primary_website'
            ));

            $fieldset = $form->addFieldset('customer_orders_addresses', array('legend' => $helper->__('Move Customer Orders And Addresses')));

            if ($model->getPrimaryWebsite() != null) {
                $hasOrdersToMove = false;
                $hasAddressesToMove = false;
                foreach ($stats as $websiteId => $data) {
                    if ($websiteId != $model->getPrimaryWebsite()) {
                        if ($data['orders_count'] > 0) {
                            $hasOrdersToMove = true;
                        }
                        if ($data['addresses_count'] > 0) {
                            $hasAddressesToMove = true;
                        }

                        if ($hasOrdersToMove && $hasAddressesToMove) break;
                    }
                }

                if ($hasOrdersToMove) {
                    $fieldset->addField('move_orders', 'checkbox', array(
                        'label' => $helper->__('Move Orders'),
                        'name' => 'move_orders'
                    ));

                    $model->setMoveOrders(true);
                }

                if ($hasAddressesToMove) {
                    $fieldset->addField('move_addresses', 'checkbox', array(
                        'label' => $helper->__('Move Addresses'),
                        'name' => 'move_addresses'
                    ));

                    $model->setMoveAddresses(true);
                }

                $fieldset = $form->addFieldset('customer_attributes', array('legend' => $helper->__('Move Customer Attributes')));

                $hasAttributesToMove = false;
                foreach ($stats[$model->getPrimaryWebsite()]['attributes'] as $key => $data) {
                    $customerAttributes = array(
                        array('value' => $data, 'label' => '<strong>(' . $model->getPrimaryWebsite() . ') ' . $data . '</strong>'),
                    );

                    foreach (Mage::app()->getWebsites() as $website) {
                        if (
                            $website->getId() == $model->getPrimaryWebsite()
                            || !isset($stats[$website->getId()])
                        ) continue;

                        if (trim($data) != trim($stats[$website->getId()]['attributes'][$key])) {
                            $value = $stats[$website->getId()]['attributes'][$key];
                            $customerAttributes[] = array('value' => $value, 'label' => '(' . $website->getId() . ') ' . $value);
                        }
                    }

                    if (count($customerAttributes) > 1) {
                        $hasAttributesToMove = true;

                        $fieldset->addField('attribute_' . $key, 'radios', array(
                            'label' => $key,
                            'name' => 'attribute[' . $key . ']',
                            'values' => $customerAttributes,
                            'disabled' => false,
                            'readonly' => false,
                        ));
                    } else {
                        $fieldset->addField('attribute_' . $key, 'text', array(
                            'label' => $key,
                            'name' => 'attribute[' . $key . ']',
                            'value' => $customerAttributes[0]['value'],
                            'disabled' => true,
                            'readonly' => true
                        ));

                        $model->setData('attribute_' . $key, $customerAttributes[0]['value']);
                    }
                }

                if (!$hasOrdersToMove && !$hasAddressesToMove && !$hasAttributesToMove) {
                    $fieldset = $form->addFieldset('customer_delete', array('legend' => $helper->__('Delete Other Customers')));

                    $fieldset->addField('delete', 'checkbox', array(
                        'label' => $helper->__('Delete Other Customers'),
                        'name' => 'delete',
                        'checked' => true,
                    ));

                    $model->setDelete(true);
                }
            }
        }

        $form->setValues($model);

        return parent::_prepareForm();
    }
}