<?php

class BI_Extra_Model_Mysql4_Extra extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the extra_id refers to the key field in your database table.
        $this->_init('extra/extra', 'extra_id');
    }
}