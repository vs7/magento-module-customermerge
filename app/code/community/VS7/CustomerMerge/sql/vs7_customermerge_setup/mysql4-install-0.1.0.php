<?php
$installer = $this;

$installer->startSetup();

$this->getConnection()->dropTable($this->getTable('vs7_customermerge/customer'));
$table = $installer->getConnection()
    ->newTable($installer->getTable('vs7_customermerge/customer'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'ID')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
    ), 'Email')
    ->addColumn('primary_website', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Primary Website');

$installer->getConnection()->createTable($table);

$installer->endSetup();