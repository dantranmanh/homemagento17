<?php

class MW_Advancedproductoption_Model_Advancedproductoption extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('advancedproductoption/advancedproductoption');
    }
}