<?php

class VS7_CustomerMerge_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('vs7_customermergeCustomerGrid');
        $this->setDefaultSort('email');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('vs7_customermerge/customer')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('email',
            array(
                'header' => $this->__('Email'),
                'align' => 'left',
                'width' => '150px',
                'index' => 'email',
                'filter' => false,
            )
        );

        foreach (Mage::app()->getWebsites() as $website) {
            $this->addColumn($website->getId(),
                array(
                    'header' => $website->getName(),
                    'align' => 'left',
                    'index' => $website->getCode(),
                    'filter' => false,
                    'renderer' => 'vs7_customermerge/adminhtml_widget_grid_column_renderer_customer'
                )
            );
        }

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getEmail()));
    }
}