<?php

class VS7_CustomerMerge_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $_customers = array();
    private $_excludeAttributes = array(
        'email',
        'website_id',
        'entity_id',
        'entity_type_id',
        'attribute_set_id',
        'increment_id',
        'store_id',
        'created_at',
        'updated_at',
        'is_active',
        'created_in',
        'password_hash',
        'default_billing',
        'default_shipping',
        'is_segment_aliased'
    );

    public function getStats($email)
    {
        if (isset($this->_customers[$email])) return $this->_customers[$email];

        $allAttributes = array();

        foreach (Mage::app()->getWebsites() as $website) {
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId($website->getid())
                ->loadByEmail($email);
            if ($customer->getId() == null) continue;

            $customerTotals = Mage::getResourceModel('sales/sale_collection')
                ->setOrderStateFilter(Mage_Sales_Model_Order::STATE_CANCELED, true)
                ->setCustomerFilter($customer)
                ->load()
                ->getTotals();

            $orders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $customer->getId())
                ->addAttributeToSort('created_at', 'DESC')
                ->setPageSize(1);

            $this->_customers[$email][$website->getid()] = array(
                'website_name' => $website->getName(),
                'customer_id' => $customer->getId(),
                'lifetime_sales' => $customerTotals->getLifetime(),
                'addresses_count' => $customer->getAddressesCollection()->getSize(),
                'orders_count' => $customerTotals->getNumOrders(),
                'last_order_date' => $orders->getFirstItem()->getCreatedAt(),
                'created_at' => $customer->getCreatedAt(),
                'updated_at' => $customer->getUpdatedAt()
            );

            foreach ($customer->getData() as $key => $value) {
                if (!in_array($key, $this->_excludeAttributes)) {
                    $this->_customers[$email][$website->getid()]['attributes'][$key] = $value;
                    if (!in_array($key, $allAttributes)) $allAttributes[] = $key;
                }
            }
        }

        foreach ($this->_customers[$email] as $websiteId => $data) {
            foreach ($allAttributes as $attributeName) {
                if (!isset($data['attributes'][$attributeName])) {
                    $this->_customers[$email][$websiteId]['attributes'][$attributeName] = null;
                }
            }
        }

        return $this->_customers[$email];
    }

    public function moveOrders($email, $websiteId)
    {
        $primaryCustomer = Mage::getModel('customer/customer')
            ->setWebsiteId($websiteId)
            ->loadByEmail($email);

        if ($primaryCustomer->getId() == null) return;

        foreach (Mage::app()->getWebsites() as $website) {
            if ($website->getId() == $websiteId) continue;

            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId($website->getId())
                ->loadByEmail($email);

            if ($customer->getId() == null) continue;

            $orders = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('customer_id', $customer->getId());
            foreach ($orders as $order) {
                $order->setCustomerId($primaryCustomer->getId())->save();
            }
        }
    }

    public function moveAddresses($email, $websiteId)
    {
        $primaryCustomer = Mage::getModel('customer/customer')
            ->setWebsiteId($websiteId)
            ->loadByEmail($email);

        if ($primaryCustomer->getId() == null) return;

        foreach (Mage::app()->getWebsites() as $website) {
            if ($website->getId() == $websiteId) continue;

            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId($website->getId())
                ->loadByEmail($email);

            if ($customer->getId() == null) continue;

            $addresses = $customer->getAddressesCollection();
            foreach ($addresses as $address) {
                $address->setCustomerId($primaryCustomer->getId())->save();
            }
        }
    }

    public function setAttributes($email, $attributes)
    {
        foreach (Mage::app()->getWebsites() as $website) {
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId($website->getid())
                ->loadByEmail($email);
            if ($customer->getId() == null) continue;

            foreach ($attributes as $key => $value) {
                $customer->setData($key, $value);
            }

            $customer->save();
        }
    }

    public function delete($email, $websiteId)
    {
        foreach (Mage::app()->getWebsites() as $website) {
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId($website->getid())
                ->loadByEmail($email);
            if ($customer->getId() == null) continue;
            if ($website->getId() == $websiteId) continue;

            $customer->setIsDeleteable(true);
            $customer->delete();
        }
    }
}