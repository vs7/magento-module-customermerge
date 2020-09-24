<?php

class VS7_CustomerMerge_Block_Adminhtml_Customer_Edit_Form_Renderer_Fieldset_Website extends Varien_Data_Form_Element_Abstract
{
    public function getElementHtml()
    {
        $html = '<div class="vs7_customermerge-website__container">';

        $myCustomer = Mage::registry('current_customer');
        $stats = Mage::helper('vs7_customermerge')->getStats($myCustomer->getEmail());

        foreach ($stats as $websiteId => $data) {
            $info = '<strong>id: </strong>' . $data['customer_id'] . "<br />\r\n";
            $info .= '<strong>created_at: </strong>' . $data['created_at'] . "<br />\r\n";
            $info .= '<strong>updated_at: </strong>' . $data['updated_at'] . "<br />\r\n";
            $info .= '<strong>orders: </strong>' . $data['orders_count'] . "<br />\r\n";
            $info .= '<strong>last order: </strong>' . $data['last_order_date'] . "<br />\r\n";
            $info .= '<strong>lifetime sales: </strong>' . $data['lifetime_sales'] . "<br />\r\n";
            $info .= '<strong>addresses: </strong>' . $data['addresses_count'] . "<br />\r\n";

            $active = '';
            $activeWebsite = '';
            if ($this->getValue() == $websiteId) {
                $active = 'checked="checked"';
                $activeWebsite = 'vs7_customermerge-website--active';
            }

            $html .= '<div class="vs7_customermerge-website entry-edit ' . $activeWebsite . '">
                <div class="vs7_customermerge-website__title entry-edit-head">
                    <h4 class="icon-head head-edit-form fieldset-legend">
                    <input type="radio" name="' . $this->getName() . '" value="' . $websiteId . '" id="' . $this->getHtmlId() . $websiteId . '" ' . $active . '/>
                    <label class="inline" for="' . $this->getHtmlId() . $websiteId . '">(' . $websiteId . ') ' . $data['website_name'] . '</label></h4>
                </div>';
            $html .= '<div class="vs7_customermerge-website__data">' . $info . '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }
}