<?php

class BI_Extra_Model_Extra extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('extra/extra');
    }
}