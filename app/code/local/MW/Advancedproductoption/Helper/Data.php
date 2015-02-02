<?php

class MW_Advancedproductoption_Helper_Data extends Mage_Core_Helper_Abstract
{
  const EE = 3;
  const PE = 2;
  const CE = 1;
  const ENTERPRISE_COMPANY = 'Enterprise';
  const PROFESSIONAL_DESIGN = "pro";

  protected static $_platform = 0;
  public function getPlatform()
    {
            if (self::$_platform == 0) {
            $pathToClaim = BP . DS . "app" . DS . "etc" . DS . "modules" . DS . self::ENTERPRISE_COMPANY . "_" . self::ENTERPRISE_COMPANY .  ".xml";
            $pathToEEConfig = BP . DS . "app" . DS . "code" . DS . "core" . DS . self::ENTERPRISE_COMPANY . DS . self::ENTERPRISE_COMPANY . DS . "etc" . DS . "config.xml";
            $isCommunity = !file_exists($pathToClaim) || !file_exists($pathToEEConfig);
            if ($isCommunity) {
                 self::$_platform = self::CE;
            } else {
                $_xml = @simplexml_load_file($pathToEEConfig,'SimpleXMLElement', LIBXML_NOCDATA);
                if(!$_xml===FALSE) {
                    $package = (string)$_xml->default->design->package->name;
                    $theme = (string)$_xml->install->design->theme->default;
                    $skin = (string)$_xml->stores->admin->design->theme->skin;
                    $isProffessional = ($package == self::PROFESSIONAL_DESIGN) && ($theme == self::PROFESSIONAL_DESIGN) && ($skin == self::PROFESSIONAL_DESIGN);
                    if ($isProffessional) {
                        self::$_platform = self::PE;
                        return self::$_platform;
                    }
                }
                self::$_platform = self::EE;
            }
        }
        return self::$_platform;
    }
	public function checkCustomerGroups($group_id, $string_customer_groups) 
  	{
  		$enabled_module = Mage::getStoreConfig('advancedproductoption/config/enabled');	
  		if(!$enabled_module)return true;
  		if($string_customer_groups == '') return true;
  		
  		$array_customer_groups = explode(',',$string_customer_groups);
  		if(in_array($group_id, $array_customer_groups)) return true;
  		else return false;
  		
  	}
  	// check qty > 0 display
  	// return 1 la co show ra option
  	// return 0 la khong show ra option
	public function checkOptionQty($option) 
  	{
  		$option_id = $option->getId();
  		$enabled_qty = Mage::getStoreConfig('advancedproductoption/config/enabled_qty');
  		$enabled_qty_option = $option->getMwQtyInput();
  		if(!$enabled_qty || !$enabled_qty_option) return 1;
  		$result = 0;
  		$values = Mage::getResourceModel('catalog/product_option_value_collection')->addFieldToFilter('option_id', $option_id);
  		if(sizeof($values)> 0){
	  		foreach ($values as $value) {
	  			$qty = $value->getMwQty();
	  			if($qty > 0) $result = 1;  
	  		}
  		}else{
  			$result = 1;
  			return $result;
  		}
  		return $result;
  	}
  	// return 1 la ok
  	// return 0 la loi
  	// ham kiem tra option co du so luong de ban ko?
  	// kieu type abstract
	public function checkOptionQtyCart($option,$optionValue,$mw_data,$qty_product)
	{
		 $mw_option_id = $option->getId();
		 $check = 1;
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
			 	if ($_result = $option->getValueById($_value)) {
	                    if($mw_qty != -99){
	                    	$mw_qty_base = (int)$_result ->getMwQty();
	                    	if($mw_qty *$qty_product > $mw_qty_base) $check = 0;
	                    	
	                    }
                }
			 }
			 return $check;

		 }else{
		 	$mw_name = 'mw_options_'.$mw_option_id.'_qty';
            $mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$optionValue.'_qty';
            $mw_qty = -99;
            if(isset($mw_data[$mw_name]) && $mw_data[$mw_name] != ''){
                $mw_qty = (int)$mw_data[$mw_name];
                	
            }else if(isset($mw_data[$mw_option_name_check]) && $mw_data[$mw_option_name_check] != ''){
                $mw_qty = (int)$mw_data[$mw_option_name_check];
            }
		 	if ($_result = $option->getValueById($optionValue)) {
		 		
		 		if($mw_qty != -99){
                    $mw_qty_base = (int)$_result ->getMwQty();
                    if($mw_qty *$qty_product > $mw_qty_base) $check = 0;
                    
                }
            } 
			return $check;
		 	
		 }
		 return $check;
	}
	// return 1 la ok
  	// return 0 la loi
  	// ham kiem tra option co du so luong de ban ko?
  	// kieu type select
	public function checkOptionQtyInput($option,$optionValue,$mw_data)
	{
		 $mw_option_id = $option->getId();
		 $check = 1;
		 $qty_product = (int)$mw_data['qty'];
		 if(sizeof($optionValue) >0){
			  foreach ($optionValue as $_value) {
			  	$mw_name = 'mw_options_'.$mw_option_id.'_qty';
                $mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$_value.'_qty';
                $mw_qty = -99;
                if(isset($mw_data[$mw_name]) && $mw_data[$mw_name] != ''){
                	$mw_qty = (int)$mw_data[$mw_name];
                	
                }else if(isset($mw_data[$mw_option_name_check]) && $mw_data[$mw_option_name_check] != ''){
                	$mw_qty = (int)$mw_data[$mw_option_name_check];
                }
			 	if ($_result = $option->getValueById($_value)) {
	                    if($mw_qty != -99){
	                    	$mw_qty_base = (int)$_result ->getMwQty();
	                    	if($mw_qty *$qty_product > $mw_qty_base) $check = 0;
	                    	
	                    }
                }
			 }
			 return $check;

		 }
		 return $check;
	}
	
	const MYCONFIG = "advancedproductoption/config/enabled";
	const MYNAME = "MW_Advancedproductoption";
	
	public function myConfig(){
    	return self::MYCONFIG;
    }
	
	function disableConfig()
	{
			Mage::getSingleton('core/config')->saveConfig($this->myConfig(),0); 			
			Mage::getModel('core/config')->saveConfig("advanced/modules_disable_output/".self::MYNAME,1);	
			 Mage::getConfig()->reinit();
	}	

}