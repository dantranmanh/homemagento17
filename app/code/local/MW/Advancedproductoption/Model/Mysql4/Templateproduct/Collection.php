<?php

class MW_Advancedproductoption_Model_Mysql4_Templateproduct_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('advancedproductoption/templateproduct');
    }
}