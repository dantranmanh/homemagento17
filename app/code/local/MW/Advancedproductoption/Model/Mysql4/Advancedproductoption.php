<?php

class MW_Advancedproductoption_Model_Mysql4_Advancedproductoption extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the advancedproductoption_id refers to the key field in your database table.
        $this->_init('advancedproductoption/advancedproductoption', 'advancedproductoption_id');
    }
}