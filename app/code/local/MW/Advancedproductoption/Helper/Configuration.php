<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper for fetching properties by product configurational item
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class MW_Advancedproductoption_Helper_Configuration extends Mage_Catalog_Helper_Product_Configuration
{
    /**
     * Retrieves product configuration options
     *
     * @param Mage_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return array
     */
    public function getCustomOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item)
    {
        $product = $item->getProduct();
        $options = array();
        $optionIds = $item->getOptionByCode('option_ids');
        $mw_data = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
        if ($optionIds) {
            $options = array();
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option) {
                    $itemOption = $item->getOptionByCode('option_' . $option->getId());
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItem($item)
                        ->setConfigurationItemOption($itemOption);
                    if ('file' == $option->getType()) {
                        $downloadParams = $item->getFileDownloadParams();
                        if ($downloadParams) {
                            $url = $downloadParams->getUrl();
                            if ($url) {
                                $group->setCustomOptionDownloadUrl($url);
                            }
                            $urlParams = $downloadParams->getUrlParams();
                            if ($urlParams) {
                                $group->setCustomOptionUrlParams($urlParams);
                            }
                        }
                    }
                    $mw_value = $group->getFormattedOptionValue($itemOption->getValue());
                    $mw_print_value = $group->getPrintableOptionValue($itemOption->getValue());
                    if($option->getType() == 'drop_down' || $option->getType() == 'radio'||$option->getType() == 'checkbox'|| $option->getType() == 'multiple')
                    {
                    	$mw_value = $this->getMwFormattedOptionValue($option,$itemOption->getValue(),$mw_data,$group);
                    	$mw_print_value = $this ->getMwPrintableOptionValue($option,$itemOption->getValue(),$mw_data,$group);
                    }
                    $options[] = array(
                        'label' => $option->getTitle(),
                        //'value' => $group->getFormattedOptionValue($itemOption->getValue()),
                        //'print_value' => $group->getPrintableOptionValue($itemOption->getValue()),
						'value' => $mw_value,
                        'print_value' => $mw_print_value,
                        'option_id' => $option->getId(),
                        'option_type' => $option->getType(),
                        'custom_view' => $group->isCustomizedView()
                    );
                }
            }
        }
        $addOptions = $item->getOptionByCode('additional_options');
        if ($addOptions) {
            $options = array_merge($options, unserialize($addOptions->getValue()));
        }

        return $options;
    }
	public function getMwFormattedOptionValue($option,$optionValue,$mw_data,$group)
	{
		 $result = '';
		 $mw_option_id = $option->getId();
		 $check = 0;
		 if(sizeof(explode(',', $optionValue)) >1){
			  foreach (explode(',', $optionValue) as $_value) {
			  	$mw_name = 'mw_options_'.$mw_option_id.'_qty';
                $mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$_value.'_qty';
                $mw_qty_show = ''; 
                if(isset($mw_data[$mw_name]) && $mw_data[$mw_name] != ''){
                	$check = 1;
                	$mw_qty_show = $mw_data[$mw_name].' x ';
                	
                }else if(isset($mw_data[$mw_option_name_check]) && $mw_data[$mw_option_name_check] != ''){
                	$check = 1;
                	$mw_qty_show = $mw_data[$mw_option_name_check].' x ';
                }
			 	if ($_result = $option->getValueById($_value)) {
	                    $result .= $mw_qty_show.$_result->getTitle() . ', ';
                }else{
                	$result = '';
                }
			 }
			 if($check == 0) return $group->getFormattedOptionValue($optionValue);
			 else return Mage::helper('core/string')->substr($result, 0, -2);
		 }else{
		 	$mw_name = 'mw_options_'.$mw_option_id.'_qty';
            $mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$optionValue.'_qty';
            $mw_qty_show = ''; 
            if(isset($mw_data[$mw_name]) && $mw_data[$mw_name] != ''){
                $check = 1;
                $mw_qty_show = $mw_data[$mw_name].' x ';
                	
            }else if(isset($mw_data[$mw_option_name_check]) && $mw_data[$mw_option_name_check] != ''){
                $check = 1;
                $mw_qty_show = $mw_data[$mw_option_name_check].' x ';
            }
		 	if ($_result = $option->getValueById($optionValue)) {
                $result = $mw_qty_show.$_result->getTitle();
            } else {
            	$result = '';
            }
            if($check == 0) return $group->getFormattedOptionValue($optionValue);
			else return $result;
		 	
		 }
		 return $group->getFormattedOptionValue($optionValue);	
	}
	public function getMwPrintableOptionValue($option,$optionValue,$mw_data,$group)
	{
		 $result = '';
		 $mw_option_id = $option->getId();
		 $check = 0;
		 if(sizeof(explode(',', $optionValue)) >1){
			  foreach (explode(',', $optionValue) as $_value) {
			  	$mw_name = 'mw_options_'.$mw_option_id.'_qty';
                $mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$_value.'_qty';
                $mw_qty_show = ''; 
                if(isset($mw_data[$mw_name]) && $mw_data[$mw_name] != ''){
                	$check = 1;
                	$mw_qty_show = $mw_data[$mw_name].' x ';
                	
                }else if(isset($mw_data[$mw_option_name_check]) && $mw_data[$mw_option_name_check] != ''){
                	$check = 1;
                	$mw_qty_show = $mw_data[$mw_option_name_check].' x ';
                }
			 	if ($_result = $option->getValueById($_value)) {
	                    $result .= $mw_qty_show.$_result->getTitle() . ', ';
                }else{
                	$result = '';
                }
			 }
			 if($check == 0) return $group->getPrintableOptionValue($optionValue);
			 else return Mage::helper('core/string')->substr($result, 0, -2);
		 }else{
		 	$mw_name = 'mw_options_'.$mw_option_id.'_qty';
            $mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$optionValue.'_qty';
            $mw_qty_show = ''; 
            if(isset($mw_data[$mw_name]) && $mw_data[$mw_name] != ''){
                $check = 1;
                $mw_qty_show = $mw_data[$mw_name].' x ';
                	
            }else if(isset($mw_data[$mw_option_name_check]) && $mw_data[$mw_option_name_check] != ''){
                $check = 1;
                $mw_qty_show = $mw_data[$mw_option_name_check].' x ';
            }
		 	if ($_result = $option->getValueById($optionValue)) {
                $result = $mw_qty_show.$_result->getTitle();
            } else {
            	$result = '';
            }
            if($check == 0) return $group->getPrintableOptionValue($optionValue);
			else return $result;
		 	
		 }
		 return $group->getPrintableOptionValue($optionValue);
		
	}
    
}
