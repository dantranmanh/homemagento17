<?php

$installer = $this;

$collection = Mage::getModel('advancedproductoption/product_option')->getCollection();

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$collection->getTable('advancedproductoption/product_option')}`;
CREATE TABLE `{$collection->getTable('advancedproductoption/product_option')}` (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `template_id` int(10) unsigned NOT NULL default '0',
  `type` varchar(50) NOT NULL default '',
  `is_require` tinyint(1) NOT NULL default '1',
  `sku` varchar(64) NOT NULL default '',
  `mw_image` varchar(255) NOT NULL default '',
  `mw_image_size_x` smallint(5) unsigned NOT NULL default '0',
  `mw_image_size_y` smallint(5) unsigned NOT NULL default '0',
  `max_characters` int(10) unsigned default NULL,
  `file_extension` varchar(50) default NULL,
  `image_size_x` smallint(5) unsigned NOT NULL,
  `image_size_y` smallint(5) unsigned NOT NULL,
  `sort_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`option_id`)
 )ENGINE=InnoDB default CHARSET=utf8;

DROP TABLE IF EXISTS `{$collection->getTable('advancedproductoption/product_option_price')}`;
CREATE TABLE `{$collection->getTable('advancedproductoption/product_option_price')}` (
  `option_price_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `price` decimal(12,4) NOT NULL default '0.00',
  `price_type` enum('fixed', 'percent') NOT NULL default 'fixed',
  PRIMARY KEY (`option_price_id`)
 )ENGINE=InnoDB default CHARSET=utf8;

DROP TABLE IF EXISTS `{$collection->getTable('advancedproductoption/product_option_title')}`;
CREATE TABLE `{$collection->getTable('advancedproductoption/product_option_title')}` (
  `option_title_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `title` VARCHAR(255) NOT NULL default '',
  PRIMARY KEY (`option_title_id`)
  )ENGINE=InnoDB default CHARSET=utf8;

DROP TABLE IF EXISTS `{$collection->getTable('advancedproductoption/product_option_type_value')}`;
CREATE TABLE `{$collection->getTable('advancedproductoption/product_option_type_value')}` (
  `option_type_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `sku` varchar(64) NOT NULL default '',
  `mw_qty` int(10) unsigned NOT NULL default '0',
  `mw_image` varchar(255) NOT NULL default '',
  `mw_image_size_x` smallint(5) unsigned NOT NULL default '0',
  `mw_image_size_y` smallint(5) unsigned NOT NULL default '0',
  `sort_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`option_type_id`)
)ENGINE=InnoDB default CHARSET=utf8;

DROP TABLE IF EXISTS `{$collection->getTable('advancedproductoption/product_option_type_price')}`;
CREATE TABLE `{$collection->getTable('advancedproductoption/product_option_type_price')}` (
  `option_type_price_id` int(10) unsigned NOT NULL auto_increment,
  `option_type_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `price` decimal(12,4) NOT NULL default '0.00',
  `price_type` enum('fixed','percent') NOT NULL default 'fixed',
  PRIMARY KEY (`option_type_price_id`)
 )ENGINE=InnoDB default CHARSET=utf8;

DROP TABLE IF EXISTS `{$collection->getTable('advancedproductoption/product_option_type_title')}`;
CREATE TABLE `{$collection->getTable('advancedproductoption/product_option_type_title')}` (
  `option_type_title_id` int(10) unsigned NOT NULL auto_increment,
  `option_type_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY (`option_type_title_id`)
 )ENGINE=InnoDB default CHARSET=utf8;
 
DROP TABLE IF EXISTS `{$collection->getTable('advancedproductoption/template')}`;
CREATE TABLE `{$collection->getTable('advancedproductoption/template')}` (
  `template_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `attribute_set` varchar(255) NOT NULL default '',
  `status` int(2) NOT NULL default '0',
  PRIMARY KEY (`template_id`)
 )ENGINE=InnoDB default CHARSET=utf8;
 
DROP TABLE IF EXISTS `{$collection->getTable('advancedproductoption/templateproduct')}`;
CREATE TABLE `{$collection->getTable('advancedproductoption/templateproduct')}` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `template_id` int(10) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`id`)
 )ENGINE=InnoDB default CHARSET=utf8;
 
");

$collection_core = Mage::getModel('catalog/product_option')->getCollection();
/*
$sql = <<<____SQL
drop procedure if exists schema_change;

delimiter ';;'
create procedure schema_change() begin

    if exists (select * from information_schema.columns where table_name = '{$collection_core->getTable('catalog/product_option_type_value')}' and column_name = 'mw_image_size_x') then
        alter table {$collection_core->getTable('catalog/product_option_type_value')} drop column `mw_image_size_x`;
    end if;
    if exists (select * from information_schema.columns where table_name = '{$collection_core->getTable('catalog/product_option_type_value')}' and column_name = 'mw_image_size_y') then
        alter table {$collection_core->getTable('catalog/product_option_type_value')} drop column `mw_image_size_y`;
    end if;

end;;

delimiter ';'
call schema_change();

drop procedure if exists schema_change;
 

____SQL;

 $write = Mage::getSingleton('core/resource')->getConnection('core_write');
 $write->query($sql);
*/
$sql_new ="";
$sql_new .="ALTER TABLE {$collection_core->getTable('catalog/product_option')} ADD  `mw_image` varchar(255) NOT NULL default '' AFTER `sku`;";
$sql_new .="ALTER TABLE {$collection_core->getTable('catalog/product_option')} ADD  `mw_image_size_x` smallint(5) unsigned NOT NULL default '0' AFTER `mw_image`;";
$sql_new .="ALTER TABLE {$collection_core->getTable('catalog/product_option')} ADD  `mw_image_size_y` smallint(5) unsigned NOT NULL default '0' AFTER `mw_image_size_x`;";

$installer->run($sql_new);

$sql ="";
$sql .="ALTER TABLE {$collection_core->getTable('catalog/product_option_type_value')} ADD  `mw_qty` int(10) unsigned NOT NULL default '0' AFTER `sku`;";
$sql .="ALTER TABLE {$collection_core->getTable('catalog/product_option_type_value')} ADD  `mw_image` varchar(255) NOT NULL default '' AFTER `mw_qty`;";
$sql .="ALTER TABLE {$collection_core->getTable('catalog/product_option_type_value')} ADD  `mw_image_size_x` smallint(5) unsigned NOT NULL default '0' AFTER `mw_image`;";
$sql .="ALTER TABLE {$collection_core->getTable('catalog/product_option_type_value')} ADD  `mw_image_size_y` smallint(5) unsigned NOT NULL default '0' AFTER `mw_image_size_x`;";

$installer->run($sql);

$installer->endSetup(); 