<?php

class MW_Advancedproductoption_Model_Templateproduct extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('advancedproductoption/templateproduct');
    }
}