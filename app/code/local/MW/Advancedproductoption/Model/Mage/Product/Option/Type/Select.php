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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product option select type
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class MW_Advancedproductoption_Model_Mage_Product_Option_Type_Select extends MW_Advancedproductoption_Model_Mage_Product_Option_Type_Default
{
    /**
     * Validate user input for option
     *
     * @throws Mage_Core_Exception
     * @param array $values All product option values, i.e. array (option_id => mixed, option_id => mixed...)
     * @return Mage_Catalog_Model_Product_Option_Type_Default
     */
    public function validateUserValue($values)
    {
        parent::validateUserValue($values);

        $option = $this->getOption();
        $value = $this->getUserValue();
        // -- code them vao-----------------------------
        $mw_data = $this->getRequest()->getData();
        $check_qty_input = Mage::helper('advancedproductoption')->checkOptionQtyInput($option,$value,$mw_data);
        // not ok error
        if($check_qty_input == 0){
        	$this->setIsValid(false);
            Mage::throwException(Mage::helper('advancedproductoption')->__('Option quantity of product is not enough.'));
        }
        
        $enabled_module = Mage::getStoreConfig('advancedproductoption/config/enabled');
        $mw_condition = empty($value) && $option->getIsRequire() && !$this->getSkipCheckRequiredOption();
    	if($enabled_module){
	        $option_id = $option->getId();
	        $check_option_qty = Mage::helper('advancedproductoption')->checkOptionQty($option);
			$group_id = Mage::getSingleton('customer/session')->getCustomerGroupId();
			$string_customer_groups = $option ->getMwCustomerGroups();
			$check_customer_groups_option = Mage::helper('advancedproductoption')->checkCustomerGroups($group_id, $string_customer_groups); 
			$option_require = 0;
			
			// co show ra option nen can check require, else thi ko can
			if($check_customer_groups_option) $option_require = $option->getIsRequire();
			else $option_require = 0;
			
			// co show ra option nen can check require, else thi ko can
			if($check_option_qty) $mw_condition = empty($value) && $option_require && !$this->getSkipCheckRequiredOption();
			else $mw_condition = $check_option_qty;
			$mw_condition_qty = 1;
			
			// -- ket thuc code them vao-----------------------------
			
			//if (empty($value) && $option->getIsRequire() && !$this->getSkipCheckRequiredOption()) {
	       // if (empty($value) && $option_require && !$this->getSkipCheckRequiredOption()) {
    	}
       if($mw_condition){
            $this->setIsValid(false);
            Mage::throwException(Mage::helper('catalog')->__('Please specify the product required option(s).'));
        }
        if (!$this->_isSingleSelection()) {
        	
            $valuesCollection = $option->getOptionValuesByOptionId($value, $this->getProduct()->getStoreId())
                ->load();
            if ($valuesCollection->count() != count($value)) {
                $this->setIsValid(false);
                Mage::throwException(Mage::helper('catalog')->__('Please specify the product required option(s).'));
            }
        }
        return $this;
    }

    /**
     * Prepare option value for cart
     *
     * @throws Mage_Core_Exception
     * @return mixed Prepared option value
     */
    public function prepareForCart()
    {
        if ($this->getIsValid() && $this->getUserValue()) {
            return is_array($this->getUserValue()) ? implode(',', $this->getUserValue()) : $this->getUserValue();
        } else {
            return null;
        }
    }

    /**
     * Return formatted option value for quote option
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getFormattedOptionValue($optionValue)
    {
        if ($this->_formattedOptionValue === null) {
            $this->_formattedOptionValue = Mage::helper('core')->htmlEscape(
                $this->getEditableOptionValue($optionValue)
            );
        }
        return $this->_formattedOptionValue;
    }

    /**
     * Return printable option value
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getPrintableOptionValue($optionValue)
    {
        return $this->getFormattedOptionValue($optionValue);
    }

    /**
     * Return wrong product configuration message
     *
     * @return string
     */
    protected function _getWrongConfigurationMessage()
    {
        return Mage::helper('catalog')->__('Some of the products below do not have all the required options. Please edit them and configure all the required options.');
    }

    /**
     * Return formatted option value ready to edit, ready to parse
     *
     * @param string $optionValue Prepared for cart option value
     * @return string
     */
    public function getEditableOptionValue($optionValue)
    {
        $option = $this->getOption();
        $result = '';
        if (!$this->_isSingleSelection()) {
            foreach (explode(',', $optionValue) as $_value) {
                if ($_result = $option->getValueById($_value)) {
                    $result .= $_result->getTitle() . ', ';
                } else {
                    if ($this->getListener()) {
                        $this->getListener()
                                ->setHasError(true)
                                ->setMessage(
                                    $this->_getWrongConfigurationMessage()
                                );
                        $result = '';
                        break;
                    }
                }
            }
            $result = Mage::helper('core/string')->substr($result, 0, -2);
        } elseif ($this->_isSingleSelection()) {
            if ($_result = $option->getValueById($optionValue)) {
                $result = $_result->getTitle();
            } else {
                if ($this->getListener()) {
                    $this->getListener()
                            ->setHasError(true)
                            ->setMessage(
                                $this->_getWrongConfigurationMessage()
                            );
                }
                $result = '';
            }
        } else {
            $result = $optionValue;
        }
        return $result;
    }

    /**
     * Parse user input value and return cart prepared value, i.e. "one, two" => "1,2"
     *
     * @param string $optionValue
     * @param array $productOptionValues Values for product option
     * @return string|null
     */
    public function parseOptionValue($optionValue, $productOptionValues)
    {
        $_values = array();
        if (!$this->_isSingleSelection()) {
            foreach (explode(',', $optionValue) as $_value) {
                $_value = trim($_value);
                if (array_key_exists($_value, $productOptionValues)) {
                    $_values[] = $productOptionValues[$_value];
                }
            }
        } elseif ($this->_isSingleSelection() && array_key_exists($optionValue, $productOptionValues)) {
            $_values[] = $productOptionValues[$optionValue];
        }
        if (count($_values)) {
            return implode(',', $_values);
        } else {
            return null;
        }
    }

    /**
     * Prepare option value for info buy request
     *
     * @param string $optionValue
     * @return mixed
     */
    public function prepareOptionValueForRequest($optionValue)
    {
        if (!$this->_isSingleSelection()) {
            return explode(',', $optionValue);
        }
        return $optionValue;
    }

    /**
     * Return Price for selected option
     *
     * @param string $optionValue Prepared for cart option value
     * @return float
     */
    public function getOptionPrice($optionValue, $basePrice,$data = null, $optionId = null)
    {
        $option = $this->getOption();
        $result = 0;

        if (!$this->_isSingleSelection()) {
            foreach(explode(',', $optionValue) as $value) {
                if ($_result = $option->getValueById($value)) {
                	if ($_result->getPriceType() == 'abs') {
                		$mw_qty = 1;
	                	$mw_option_id = $optionId;
	                	$mw_option_name = 'mw_options_'.$mw_option_id.'_qty';
	                	$mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$value.'_qty';
	                	if(isset($data[$mw_option_name]) && $data[$mw_option_name] !='') $mw_qty = (int)$data[$mw_option_name];
	                	if(isset($data[$mw_option_name_check]) && $data[$mw_option_name_check] !='') $mw_qty = (int)$data[$mw_option_name_check];
	                	if ($_result->getPriceType() == 'onetime') $mw_qty = 1;
	                	
		            	$result = $this->_getChargableOptionPrice(
	                        $_result->getPrice()* $mw_qty,
	                        $_result->getPriceType() == 'percent',
	                        $basePrice
	                    );
			        }
			        else {
	                	$mw_qty =1;
	                	$mw_option_id = $optionId;
	                	$mw_option_name = 'mw_options_'.$mw_option_id.'_qty';
	                	$mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$value.'_qty';
	                	if(isset($data[$mw_option_name]) && $data[$mw_option_name] !='') $mw_qty = (int)$data[$mw_option_name];
	                	if(isset($data[$mw_option_name_check]) && $data[$mw_option_name_check] !='') $mw_qty = (int)$data[$mw_option_name_check];
	                	if ($_result->getPriceType() == 'onetime') $mw_qty = 1;
	                	
	                    $result += $this->_getChargableOptionPrice(
	                        $_result->getPrice() * $mw_qty,
	                        $_result->getPriceType() == 'percent',
	                        $basePrice
	                    );
			        }
                } else {
                    if ($this->getListener()) {
                        $this->getListener()
                                ->setHasError(true)
                                ->setMessage(
                                    $this->_getWrongConfigurationMessage()
                                );
                        break;
                    }
                }
            }
        } elseif ($this->_isSingleSelection()) {
            if ($_result = $option->getValueById($optionValue)) {
            	if ($_result->getPriceType() == 'abs') {
            		$mw_qty =1;
	                $mw_option_id = $optionId;
	                $mw_option_name = 'mw_options_'.$mw_option_id.'_qty';
	                $mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$value.'_qty';
	                if(isset($data[$mw_option_name]) && $data[$mw_option_name] !='') $mw_qty = (int)$data[$mw_option_name];
	                if(isset($data[$mw_option_name_check]) && $data[$mw_option_name_check] !='') $mw_qty = (int)$data[$mw_option_name_check];
	                if ($_result->getPriceType() == 'onetime') $mw_qty = 1;
	                
		            $result = $this->_getChargableOptionPrice(
	                    $_result->getPrice()* $mw_qty,
	                    $_result->getPriceType() == 'percent',
	                    $basePrice
	                );
		        }
		        else {
	            	$mw_qty =1;
	                $mw_option_id = $optionId;
	                $mw_option_name = 'mw_options_'.$mw_option_id.'_qty';
	                $mw_option_name_check = 'mw_options_'.$mw_option_id.'_'.$value.'_qty';
	                if(isset($data[$mw_option_name]) && $data[$mw_option_name] !='') $mw_qty = (int)$data[$mw_option_name];
	                if(isset($data[$mw_option_name_check]) && $data[$mw_option_name_check] !='') $mw_qty = (int)$data[$mw_option_name_check];
	                if ($_result->getPriceType() == 'onetime') $mw_qty = 1;
	                	
	                $result = $this->_getChargableOptionPrice(
	                    $_result->getPrice() * $mw_qty,
	                    $_result->getPriceType() == 'percent',
	                    $basePrice
	                );
            	}
            } else {
                if ($this->getListener()) {
                    $this->getListener()
                            ->setHasError(true)
                            ->setMessage(
                                $this->_getWrongConfigurationMessage()
                            );
                }
            }
        }

        return $result;
    }

    /**
     * Return SKU for selected option
     *
     * @param string $optionValue Prepared for cart option value
     * @param string $skuDelimiter Delimiter for Sku parts
     * @return string
     */
    public function getOptionSku($optionValue, $skuDelimiter)
    {
        $option = $this->getOption();

        if (!$this->_isSingleSelection()) {
            $skus = array();
            foreach(explode(',', $optionValue) as $value) {
                if ($optionSku = $option->getValueById($value)) {
                    $skus[] = $optionSku->getSku();
                } else {
                    if ($this->getListener()) {
                        $this->getListener()
                                ->setHasError(true)
                                ->setMessage(
                                    $this->_getWrongConfigurationMessage()
                                );
                        break;
                    }
                }
            }
            $result = implode($skuDelimiter, $skus);
        } elseif ($this->_isSingleSelection()) {
            if ($result = $option->getValueById($optionValue)) {
                return $result->getSku();
            } else {
                if ($this->getListener()) {
                    $this->getListener()
                            ->setHasError(true)
                            ->setMessage(
                                $this->_getWrongConfigurationMessage()
                            );
                }
                return '';
            }
        } else {
            $result = parent::getOptionSku($optionValue, $skuDelimiter);
        }

        return $result;
    }

    /**
     * Check if option has single or multiple values selection
     *
     * @return boolean
     */
    protected function _isSingleSelection()
    {
        $_single = array(
            Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN,
            Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO
        );
        return in_array($this->getOption()->getType(), $_single);
    }
}