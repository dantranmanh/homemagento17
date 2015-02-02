<?php

$installer = $this;

$collection = Mage::getModel('advancedproductoption/product_option')->getCollection();
$collection_core = Mage::getModel('catalog/product_option')->getCollection();

$installer->startSetup();

$sql ="";
$sql .="ALTER TABLE {$collection->getTable('advancedproductoption/product_option')} ADD  `mw_qty_input` smallint(6) NOT NULL default '1' AFTER `mw_image_size_y`;";


$installer->run($sql);

$sql_new ="";
$sql_new .="ALTER TABLE {$collection_core->getTable('catalog/product_option')} ADD  `mw_qty_input` smallint(6) NOT NULL default '1' AFTER `mw_image_size_y`;";


$installer->run($sql_new);

$sql_change ="";
$sql_change .="ALTER TABLE {$collection_core->getTable('catalog/product_option_price')} CHANGE `price_type` `price_type` varchar(7) NOT NULL default 'fixed';";
$sql_change .="ALTER TABLE {$collection_core->getTable('catalog/product_option_type_price')} CHANGE `price_type` `price_type` varchar(7) NOT NULL default 'fixed';";

$sql_change .="ALTER TABLE {$collection->getTable('advancedproductoption/product_option_price')} CHANGE `price_type` `price_type` varchar(7) NOT NULL default 'fixed';";
$sql_change .="ALTER TABLE {$collection->getTable('advancedproductoption/product_option_type_price')} CHANGE `price_type` `price_type` varchar(7) NOT NULL default 'fixed';";

$installer->run($sql_change);

$installer->endSetup(); 