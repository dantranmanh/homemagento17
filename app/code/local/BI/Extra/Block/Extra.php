<?php
class BI_Extra_Block_Extra extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getExtra()     
     { 
        if (!$this->hasData('extra')) {
            $this->setData('extra', Mage::registry('extra'));
        }
        return $this->getData('extra');
        
    }
}