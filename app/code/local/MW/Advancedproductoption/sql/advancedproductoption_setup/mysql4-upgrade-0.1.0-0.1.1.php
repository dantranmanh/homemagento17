<?php

$installer = $this;

$collection = Mage::getModel('advancedproductoption/product_option')->getCollection();
$collection_core = Mage::getModel('catalog/product_option')->getCollection();

$installer->startSetup();

$sql ="";
$sql .="ALTER TABLE {$collection->getTable('advancedproductoption/product_option')} ADD  `mw_customer_groups` varchar(255) NOT NULL default '' AFTER `mw_image_size_y`;";
$sql .="ALTER TABLE {$collection->getTable('advancedproductoption/product_option')} ADD  `mw_description` text NOT NULL default '' AFTER `mw_customer_groups`;";


$installer->run($sql);

$sql_new ="";
$sql_new .="ALTER TABLE {$collection_core->getTable('catalog/product_option')} ADD  `mw_customer_groups` varchar(255) NOT NULL default '' AFTER `mw_image_size_y`;";
$sql_new .="ALTER TABLE {$collection_core->getTable('catalog/product_option')} ADD  `mw_description` text NOT NULL default '' AFTER `mw_customer_groups`;";


$installer->run($sql_new);



$installer->endSetup(); 