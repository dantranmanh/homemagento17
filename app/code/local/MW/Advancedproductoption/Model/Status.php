<?php

class MW_Advancedproductoption_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('advancedproductoption')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('advancedproductoption')->__('Disabled')
        );
    }
	static public function getLabel($status)
    {
    	$options = self::getOptionArray();
    	return $options[$status];
    }
}