<?php
class MW_Advancedproductoption_Model_Method
{
	const NORMAL				    = 1;		
    const CLICKIMAGE				= 2;
	

    public function toOptionArray()
    {
        return array(
            self::NORMAL    				=> Mage::helper('advancedproductoption')->__('Selected options'),
            self::CLICKIMAGE  			 	=> Mage::helper('advancedproductoption')->__('All options')
        );
    }

}
