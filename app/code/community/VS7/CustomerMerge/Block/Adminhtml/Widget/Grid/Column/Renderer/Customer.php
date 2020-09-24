<?php

class VS7_CustomerMerge_Block_Adminhtml_Widget_Grid_Column_Renderer_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $myCustomer)
    {
        $stats = Mage::helper('vs7_customermerge')->getStats($myCustomer->getEmail());

        $websiteId = $this->getColumn()->getId();

        if (!isset($stats[$websiteId])) return null;

        $info = '<strong>id: </strong>' . $stats[$websiteId]['customer_id'] . "<br />\r\n";
        $info .= '<strong>created_at: </strong>' . $stats[$websiteId]['created_at'] . "<br />\r\n";
        $info .= '<strong>updated_at: </strong>' . $stats[$websiteId]['updated_at'] . "<br />\r\n";
        $info .= '<strong>orders: </strong>' . $stats[$websiteId]['orders_count'] . "<br />\r\n";
        $info .= '<strong>last order: </strong>' . $stats[$websiteId]['last_order_date'] . "<br />\r\n";
        $info .= '<strong>lifetime sales: </strong>' . $stats[$websiteId]['lifetime_sales'] . "<br />\r\n";
        $info .= '<strong>addresses: </strong>' . $stats[$websiteId]['addresses_count'] . "<br />\r\n";

        return $info;
    }
}