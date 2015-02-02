<?php

class MW_Advancedproductoption_Model_Observer 
{	
	// xoa image trong cache 
 	public function runCron()
    {
    	Mage::getModel('advancedproductoption/image')->clearCache();
    }
	public function updateQtyPending($arvgs)
    {
    	$enabled_module = Mage::getStoreConfig('advancedproductoption/config/enabled');
    	if($enabled_module){
	    	$order = $arvgs->getOrder();
	    	$order_id = $order->getId();
	    	$items = Mage::getModel("sales/order")->load($order_id)->getAllVisibleItems();
	    	foreach ($items as $item) {
	    		$product = $item ->getProduct();
	    		$qty = (int)$item->getQtyOrdered();
		    	if($product['has_options']){
		    		$options = $item->getData('product_options');
		    		$options = unserialize($options);
		    		if (isset($options['options'])) {
		    			foreach ($options['options'] as $mw_option) {
		    				$this->subQtyOptionValue($mw_option,$mw_option['option_value'],$options['info_buyRequest'],$qty);
		    			}
		    		}
			    	
				}
			
	    	}
    	}
    }
    public function updateQtyCanceled($arvgs)
    {
    	$enabled_module = Mage::getStoreConfig('advancedproductoption/config/enabled');
    	if($enabled_module){
	    	$order = $arvgs->getOrder();
	    	if($order->getStatus() == 'canceled'){
	    		$order_id = $order->getId();
	    		$items = Mage::getModel("sales/order")->load($order_id)->getAllVisibleItems();
		    	foreach ($items as $item) {
			    	$product = $item ->getProduct();
			    	$qty = (int)$item->getQtyOrdered();
			    	if($product['has_options']){
			    		$options = $item->getData('product_options');
			    		$options = unserialize($options);
			    		if (isset($options['options'])) {
			    			foreach ($options['options'] as $mw_option) {
			    				$this->addQtyOptionValue($mw_option,$mw_option['option_value'],$options['info_buyRequest'],$qty);
			    			}
			    		}
				    	
					}
				
		    	}
	    	}
    	}
    }
	public function subQtyOptionValue($mw_option,$optionValue,$mw_data,$qty)
	{
		 $mw_option_id = $mw_option['option_id'];;
		 if(sizeof(explode(',', $optionValue)) >1){
			  foreach (explode(',', $optionValue) as $_value) {
			  	$mw_name = 'mw_options_'.$mw_option_id.'_qty';
                $mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$_value.'_qty';
                $mw_qty = -99;
                if(isset($mw_data[$mw_name]) && $mw_data[$mw_name] != ''){
                	$mw_qty = (int)$mw_data[$mw_name];
                	
                }else if(isset($mw_data[$mw_option_name_check]) && $mw_data[$mw_option_name_check] != ''){
                	$mw_qty = (int)$mw_data[$mw_option_name_check];
                }
			 	if ($_result = Mage::getModel('catalog/product_option_value')->load($_value)) {
	                    if($mw_qty != -99){
	                    	$mw_qty_base = (int)$_result ->getMwQty();
	                    	$this->_saveQtyTable($_value,$mw_qty_base - $qty*$mw_qty);
	                    	
	                    }
                }
			 }

		 }else{
		 	$mw_name = 'mw_options_'.$mw_option_id.'_qty';
            $mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$optionValue.'_qty';
            $mw_qty = -99;
            if(isset($mw_data[$mw_name]) && $mw_data[$mw_name] != ''){
                $mw_qty = (int)$mw_data[$mw_name];
                	
            }else if(isset($mw_data[$mw_option_name_check]) && $mw_data[$mw_option_name_check] != ''){
                $mw_qty = (int)$mw_data[$mw_option_name_check];
            }
		 	if ($_result = Mage::getModel('catalog/product_option_value')->load($optionValue)) {	
		 		if($mw_qty != -99){
                    $mw_qty_base = (int)$_result ->getMwQty();
                    $this->_saveQtyTable($optionValue,$mw_qty_base - $qty*$mw_qty);
                    
                }
            } 
		 	
		 }
	}
	public function addQtyOptionValue($mw_option,$optionValue,$mw_data,$qty)
	{
		$mw_option_id = $mw_option['option_id'];;
		if(sizeof(explode(',', $optionValue)) >1){
			  foreach (explode(',', $optionValue) as $_value) {
			  	$mw_name = 'mw_options_'.$mw_option_id.'_qty';
                $mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$_value.'_qty';
                $mw_qty = -99;
                if(isset($mw_data[$mw_name]) && $mw_data[$mw_name] != ''){
                	$mw_qty = (int)$mw_data[$mw_name];
                	
                }else if(isset($mw_data[$mw_option_name_check]) && $mw_data[$mw_option_name_check] != ''){
                	$mw_qty = (int)$mw_data[$mw_option_name_check];
                }
			 	if ($_result = Mage::getModel('catalog/product_option_value')->load($_value)) {
	                    if($mw_qty != -99){
	                    	$mw_qty_base = (int)$_result ->getMwQty();
	                    	$this->_saveQtyTable($_value,$mw_qty_base + $qty*$mw_qty);
	                    	
	                    }
                }
			 }

		 }else{
		 	$mw_name = 'mw_options_'.$mw_option_id.'_qty';
            $mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$optionValue.'_qty';
            $mw_qty = -99;
            if(isset($mw_data[$mw_name]) && $mw_data[$mw_name] != ''){
                $mw_qty = (int)$mw_data[$mw_name];
                	
            }else if(isset($mw_data[$mw_option_name_check]) && $mw_data[$mw_option_name_check] != ''){
                $mw_qty = (int)$mw_data[$mw_option_name_check];
            }
		 	if ($_result = Mage::getModel('catalog/product_option_value')->load($optionValue)) {	
		 		if($mw_qty != -99){
                    $mw_qty_base = (int)$_result ->getMwQty();
                    $this->_saveQtyTable($optionValue,$mw_qty_base + $qty*$mw_qty);
                    
                }
            } 
		 	
		 }
	}
	public function _saveQtyTable($option_type_id,$mw_qty)
	{
		$resource = Mage::getSingleton('core/resource');
    	$writeConnection = $resource->getConnection('core_write');
    	$table = $resource->getTableName('catalog/product_option_type_value');
		$query = "UPDATE {$table} SET mw_qty = '{$mw_qty}' WHERE option_type_id = ". $option_type_id;
        $writeConnection->query($query);
	}
	
	public function checkLicense($o)
	{
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules; 
		$modules2 = array_keys((array)Mage::getConfig()->getNode('modules')->children()); 
		if(!in_array('MW_Mcore', $modules2) || !$modulesArray['MW_Mcore']->is('active') || Mage::getStoreConfig('mcore/config/enabled')!=1)
		{
			Mage::helper('advancedproductoption')->disableConfig();
		}
		
	}
     
}