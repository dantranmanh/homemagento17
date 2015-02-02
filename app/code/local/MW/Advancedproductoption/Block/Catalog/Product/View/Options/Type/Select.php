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
 * Product options text type block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class MW_Advancedproductoption_Block_Catalog_Product_View_Options_Type_Select
    extends Mage_Catalog_Block_Product_View_Options_Abstract
{
    /**
     * Return html for control element
     *
     * @return string
     */
    public function getValuesHtml()
    {
	 	$check_platform = Mage::helper('advancedproductoption') ->getPlatform();
	 	if($check_platform == 2 ||  version_compare(Mage::getVersion(),'1.7.0.0','<'))
	 	{
	    	$method_show_image = Mage::getStoreConfig('advancedproductoption/config/method_show_image');
	    	$enabled_module = Mage::getStoreConfig('advancedproductoption/config/enabled');
	 		$enabled_qty = Mage::getStoreConfig('advancedproductoption/config/enabled_qty');

	        $_option = $this->getOption();
	        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $_option->getId());
	        $store = $this->getProduct()->getStore();
	
	        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN
	            || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
	            $require = ($_option->getIsRequire()) ? ' required-entry' : '';
	            $extraParams = '';
	            $select = $this->getLayout()->createBlock('core/html_select')
	                ->setData(array(
	                    'id' => 'select_'.$_option->getId(),
	                    'class' => $require.' product-custom-option'
	                ));
	            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN) {
	                $select->setName('options['.$_option->getid().']')
	                    ->addOption('', $this->__('-- Please Select --'));
	            } else {
	                $select->setName('options['.$_option->getid().'][]');
	                $select->setClass('multiselect'.$require.' product-custom-option');
	            }
	            foreach ($_option->getValues() as $_value) {
	                $priceStr = $this->_formatPrice(array(
	                    'is_percent' => ($_value->getPriceType() == 'percent') ? true : false,
	                    'pricing_type'=> $_value->getPriceType(),
	                    'pricing_value' => $_value->getPrice(true)
	                ), false);
	                if($enabled_module){
				        $mw_qty_show = '';
				    	$mw_condition_qty = $enabled_module;
				        if($enabled_qty && $_option->getMwQtyInput()){
				        	$mw_qty_show = ' ('.$_value->getMwQty().')';
				        	$mw_condition_qty = $_value->getMwQty()>0;
				        }
		                if($mw_condition_qty)
			                $select->addOption(
			                    $_value->getOptionTypeId(),
			                    $_value->getTitle() .$mw_qty_show. ' ' . $priceStr . '',
			                    array('price' => $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false))
			                );
	                }else{
		                	$select->addOption(
		                    $_value->getOptionTypeId(),
		                    $_value->getTitle() . ' ' . $priceStr . '',
		                    array('price' => $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false))
		                );
	                }
	                
	            }
	            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
	                $extraParams = ' multiple="multiple"';
	            }
	            if (!$this->getSkipJsReloadPrice()) {
	            	// them code vao--------------------------
	            	if($enabled_module){
	            		if($method_show_image == MW_Advancedproductoption_Model_Method::NORMAL){
	            			$extraParams .= ' onchange="opConfig.reloadPrice();mw_selectOptionType.showImage(this.id);" ';
	            		}else if($method_show_image == MW_Advancedproductoption_Model_Method::CLICKIMAGE){
	            			$extraParams .= ' onchange="opConfig.reloadPrice();mw_selectOptionType.showImageNew(this.id);" ';
	            		} 
	            		
	            	}else{
	            		$extraParams .= ' onchange="opConfig.reloadPrice();" ';
	            	}
	                //$extraParams .= ' onchange="opConfig.reloadPrice();mw_selectOptionType.showImage(this.id);" ';
	                //$extraParams .= ' onchange="opConfig.reloadPrice();mw_selectOptionType.showImageNew(this.id);" ';
	            }
	            $select->setExtraParams($extraParams);
	
	            if ($configValue) {
	                $select->setValue($configValue);
	            }
	
	            return $select->getHtml();
	        }
	
	        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO
	            || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
	            ) {
	            $selectHtml = '<ul id="options-'.$_option->getId().'-list" class="options-list">';
	            $require = ($_option->getIsRequire()) ? ' validate-one-required-by-name' : '';
	            $arraySign = '';
	            switch ($_option->getType()) {
	                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO:
	                    $type = 'radio';
	                    $class = 'radio';
	                    if (!$_option->getIsRequire()) {
	                    	// them code vao-----------------
	                    	$mw_class = 'mw_class_'.$_option->getId();
	                    	if($enabled_module){
	                    		if($method_show_image == MW_Advancedproductoption_Model_Method::NORMAL){
	                    			$selectHtml .= '<li><input type="radio" id="options_'.$_option->getId().'" class="'.$class.' product-custom-option" name="options['.$_option->getId().']"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();mw_selectOptionType.showImage(this.id);"') . ' value="" checked="checked" /><span class="label"><label for="options_'.$_option->getId().'">' . $this->__('None') . '</label></span></li>';
	                    		}
			            		else if($method_show_image == MW_Advancedproductoption_Model_Method::CLICKIMAGE){
			            			$selectHtml .= '<li><input type="radio" id="options_'.$_option->getId().'" class="'.$mw_class.' '.$class.' product-custom-option" name="options['.$_option->getId().']"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();mw_selectOptionType.showImageNew(this.id);"') . ' value="" checked="checked" /><span class="label"><label for="options_'.$_option->getId().'">' . $this->__('None') . '</label></span></li>';
			            		}		
	                    		
	                    	}else{
	                    		$selectHtml .= '<li><input type="radio" id="options_'.$_option->getId().'" class="'.$class.' product-custom-option" name="options['.$_option->getId().']"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();"') . ' value="" checked="checked" /><span class="label"><label for="options_'.$_option->getId().'">' . $this->__('None') . '</label></span></li>';
	                    	}
	                        //$selectHtml .= '<li><input type="radio" id="options_'.$_option->getId().'" class="'.$class.' product-custom-option" name="options['.$_option->getId().']"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();mw_selectOptionType.showImage(this.id);"') . ' value="" checked="checked" /><span class="label"><label for="options_'.$_option->getId().'">' . $this->__('None') . '</label></span></li>';
							//$selectHtml .= '<li><input type="radio" id="options_'.$_option->getId().'" class="'.$mw_class.' '.$class.' product-custom-option" name="options['.$_option->getId().']"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();mw_selectOptionType.showImageNew(this.id);"') . ' value="" checked="checked" /><span class="label"><label for="options_'.$_option->getId().'">' . $this->__('None') . '</label></span></li>';
	                    }
	                    break;
	                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX:
	                    $type = 'checkbox';
	                    $class = 'checkbox';
	                    $arraySign = '[]';
	                    break;
	            }
	            $count = 1;
	            foreach ($_option->getValues() as $_value) {
	                $count++;
	
	                $priceStr = $this->_formatPrice(array(
	                    'is_percent' => ($_value->getPriceType() == 'percent') ? true : false,
	                    'pricing_type'=> $_value->getPriceType(),
	                    'pricing_value' => $_value->getPrice(true)
	                ));
	
	                $htmlValue = $_value->getOptionTypeId();
	                if ($arraySign) {
	                    $checked = (is_array($configValue) && in_array($htmlValue, $configValue)) ? 'checked' : '';
	                } else {
	                    $checked = $configValue == $htmlValue ? 'checked' : '';
	                }
	           	    // them code vao --------------------
	           	    $mw_class = 'mw_class_'.$_option->getId();
	           	    $image_size_config = Mage::getStoreConfig('advancedproductoption/config/image_size');
					$array_image_size_config = explode(',',$image_size_config);	
					$width_config = 80;
					$height_config = 80;
					if(isset($array_image_size_config[0])) $width_config = $array_image_size_config[0];
					if(isset($array_image_size_config[1])) $height_config = $array_image_size_config[1];
					$option_id = $_option->getId();
	            	$option_type_id = "mw_image_select_".$option_id.'_'.$_value ->getOptionTypeId();
					$class_option = "mw_class_image_select_".$option_id;
					$image_name = $_value ->getMwImage();
					$image = '';
					if($image_name != '')
					{	
						$width = $_value->getMwImageSizeX();
						$height = $_value->getMwImageSizeY();
						$mw_title = $_value->getTitle();
						if($width == 0 || $height == 0) {
							$width = (int)$width_config;
							$height = (int)$height_config;
						}
						$image_url = Mage::helper('advancedproductoption/image')->init($image_name)->keepAspectRatio(false)->constrainOnly(false)->keepFrame(false)->resize($width,$height);
						$image = '<img id ="'.$option_type_id.'" class ="'.$class_option.'" style="vertical-align: middle;margin-left: 10px; margin-top: 10px;" onclick="mw_selectOptionType.clickImage(this.id);" title="'.$mw_title.'" alt="'.$mw_title.'" src="'.$image_url.'"  width="'.$width.'" height="'.$height.'" />';
					}
					
	                if($enabled_module){
		                $mw_qty_show = '';
				    	$mw_condition_qty = $enabled_module;
				        if($enabled_qty && $_option->getMwQtyInput()){
				        	$mw_qty_show = ' ('.$_value->getMwQty().')';
				        	$mw_condition_qty = $_value->getMwQty()>0;
				        }
	                	$mw_qty = '';
	                	if($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX && $enabled_qty && $_option->getMwQtyInput())
	                	$mw_qty ='<span class="qty-holder"><label>Qty: <input type="text" onkeypress="if(event.keyCode==13){opConfig.reloadPrice(); }" onchange="opConfig.reloadPrice(); " name="mw_options_'
			                     . $_option->getId().'_'.$_value ->getOptionTypeId().'_qty" id="mw_options_'.$_option->getId().'_'.$_value ->getOptionTypeId().'_qty" maxlength="12" value="1" class="input-text qty validate-greater-than-zero" /></label></span>';
	                	
	                	if($method_show_image == MW_Advancedproductoption_Model_Method::NORMAL){
	                		 if($mw_condition_qty)
	                		 $selectHtml .= '<li>' .
	                               '<input type="'.$type.'" class="'.$class.' '.$require.' product-custom-option"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();mw_selectOptionType.showImage(this.id);"') . ' name="options['.$_option->getId().']'.$arraySign.'" id="options_'.$_option->getId().'_'.$count.'" value="' . $htmlValue . '" ' . $checked . ' price="' . $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />' .
	                               '<span class="label"><label for="options_'.$_option->getId().'_'.$count.'">'.$_value->getTitle().$mw_qty_show.' '.$priceStr.'</label></span>'.$mw_qty;
	                	} 
			            else if($method_show_image == MW_Advancedproductoption_Model_Method::CLICKIMAGE){
			            	 if($mw_condition_qty)
			            	 $selectHtml .= '<li class="mw_item">' .$image.
	                               '<div><input type="'.$type.'" class="'.$mw_class.' '.$class.' '.$require.' product-custom-option"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();mw_selectOptionType.showImageNew(this.id);"') . ' name="options['.$_option->getId().']'.$arraySign.'" id="options_'.$_option->getId().'_'.$count.'" value="' . $htmlValue . '" ' . $checked . ' price="' . $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />' .
	                               '<span class="label"><label for="options_'.$_option->getId().'_'.$count.'">'.$_value->getTitle().$mw_qty_show.' '.$priceStr.'</label></span>'.$mw_qty.'</div>';
			            }
	                   
	                }else{
	                	$selectHtml .= '<li>' .
	                               '<input type="'.$type.'" class="'.$class.' '.$require.' product-custom-option"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();"') . ' name="options['.$_option->getId().']'.$arraySign.'" id="options_'.$_option->getId().'_'.$count.'" value="' . $htmlValue . '" ' . $checked . ' price="' . $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />' .
	                               '<span class="label"><label for="options_'.$_option->getId().'_'.$count.'">'.$_value->getTitle().' '.$priceStr.'</label></span>';
	                }
	                /*
	                $selectHtml .= '<li>' .
	                               '<input type="'.$type.'" class="'.$class.' '.$require.' product-custom-option"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();mw_selectOptionType.showImage(this.id);"') . ' name="options['.$_option->getId().']'.$arraySign.'" id="options_'.$_option->getId().'_'.$count.'" value="' . $htmlValue . '" ' . $checked . ' price="' . $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />' .
	                              // '<input type="'.$type.'" class="'.$mw_class.' '.$class.' '.$require.' product-custom-option"' . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();mw_selectOptionType.showImageNew(this.id);"') . ' name="options['.$_option->getId().']'.$arraySign.'" id="options_'.$_option->getId().'_'.$count.'" value="' . $htmlValue . '" ' . $checked . ' price="' . $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />' .
	                               '<span class="label"><label for="options_'.$_option->getId().'_'.$count.'">'.$_value->getTitle().' '.$priceStr.'</label></span>';
	                */
	                if ($_option->getIsRequire()) {
	                    $selectHtml .= '<script type="text/javascript">' .
	                                    '$(\'options_'.$_option->getId().'_'.$count.'\').advaiceContainer = \'options-'.$_option->getId().'-container\';' .
	                                    '$(\'options_'.$_option->getId().'_'.$count.'\').callbackFunction = \'validateOptionsCallback\';' .
	                                   '</script>';
	                }
	                $selectHtml .= '</li>';
	            }
	            $selectHtml .= '</ul>';
	
	            return $selectHtml;
	        }
	 	}else{
	 		// code dung cho ban 1700---------------------------------------------------------------------
	 		$method_show_image = Mage::getStoreConfig('advancedproductoption/config/method_show_image');
	    	$enabled_module = Mage::getStoreConfig('advancedproductoption/config/enabled');
	    	$enabled_qty = Mage::getStoreConfig('advancedproductoption/config/enabled_qty');
	        
		 	$_option = $this->getOption();
	        $configValue = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $_option->getId());
	        $store = $this->getProduct()->getStore();
	
	        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN
	            || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
	            $require = ($_option->getIsRequire()) ? ' required-entry' : '';
	            $extraParams = '';
	            $select = $this->getLayout()->createBlock('core/html_select')
	                ->setData(array(
	                    'id' => 'select_'.$_option->getId(),
	                    'class' => $require.' product-custom-option'
	                ));
	            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN) {
	                $select->setName('options['.$_option->getid().']')
	                    ->addOption('', $this->__('-- Please Select --'));
	            } else {
	                $select->setName('options['.$_option->getid().'][]');
	                $select->setClass('multiselect'.$require.' product-custom-option');
	            }
	            foreach ($_option->getValues() as $_value) {
	                $priceStr = $this->_formatPrice(array(
	                    'is_percent'    => ($_value->getPriceType() == 'percent'),
	                    'pricing_type'=> $_value->getPriceType(),
	                    'pricing_value' => $_value->getPrice(($_value->getPriceType() == 'percent'))
	                ), false);
	                if($enabled_module){
		                $mw_qty_show = '';
				    	$mw_condition_qty = $enabled_module;
				        if($enabled_qty && $_option->getMwQtyInput()){
				        	$mw_qty_show = ' ('.$_value->getMwQty().')';
				        	$mw_condition_qty = $_value->getMwQty()>0;
				        }
		                if($mw_condition_qty)
			                $select->addOption(
			                    $_value->getOptionTypeId(),
			                    $_value->getTitle() .$mw_qty_show. ' ' . $priceStr . '',
			                    array('price' => $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false))
			                );
	                }else{
	                	$select->addOption(
			                    $_value->getOptionTypeId(),
			                    $_value->getTitle() . ' ' . $priceStr . '',
			                    array('price' => $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false))
			                );
	                }
	            }
	            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
	                $extraParams = ' multiple="multiple"';
	            }
	            if (!$this->getSkipJsReloadPrice()) {
	           		 // them code vao--------------------------
	            	if($enabled_module){
	            		if($method_show_image == MW_Advancedproductoption_Model_Method::NORMAL){
	            			$extraParams .= ' onchange="opConfig.reloadPrice();mw_selectOptionType.showImage(this.id);" ';
	            		}else if($method_show_image == MW_Advancedproductoption_Model_Method::CLICKIMAGE){
	            			$extraParams .= ' onchange="opConfig.reloadPrice();mw_selectOptionType.showImageNew(this.id);" ';
	            		} 
	            		
	            	}else{
	            		$extraParams .= ' onchange="opConfig.reloadPrice();" ';
	            	}
	                // ket thuc them code vao---------
	            }
	            $select->setExtraParams($extraParams);
	
	            if ($configValue) {
	                $select->setValue($configValue);
	            }
	
				 return $select->getHtml();
	        }
	
	        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO
	            || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
	            ) { 
	            $selectHtml = '<ul id="options-'.$_option->getId().'-list" class="options-list">';
	            $require = ($_option->getIsRequire()) ? ' validate-one-required-by-name' : '';
	            $arraySign = '';
	            switch ($_option->getType()) {
	                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO:
	                    $type = 'radio';
	                    $class = 'radio';
	                    if (!$_option->getIsRequire()) {
	                    	// them code vao------------------
	                    	$mw_class = 'mw_class_'.$_option->getId();
	                    	if($enabled_module){
	                    		if($method_show_image == MW_Advancedproductoption_Model_Method::NORMAL){
	                    			$selectHtml .= '<li><input type="radio" id="options_' . $_option->getId() . '" class="'
		                            . $class . ' product-custom-option" name="options[' . $_option->getId() . ']"'
		                            . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();mw_selectOptionType.showImage(this.id);"')
		                            . ' value="" checked="checked" /><span class="label"><label for="options_'
		                            . $_option->getId() . '">' . $this->__('None') . '</label></span></li>';
	                    		}
			            		else if($method_show_image == MW_Advancedproductoption_Model_Method::CLICKIMAGE){
			            			$selectHtml .= '<li ><input type="radio" id="options_' . $_option->getId() . '" class="'
		                            .$mw_class.' '. $class . ' product-custom-option" name="options[' . $_option->getId() . ']"'
		                            . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();mw_selectOptionType.showImageNew(this.id);"')
		                            . ' value="" checked="checked" /><span class="label"><label for="options_'
		                            . $_option->getId() . '">' . $this->__('None') . '</label></span></li>';
			            		}		
	                    		
	                    	}else{
	                    		$selectHtml .= '<li><input type="radio" id="options_' . $_option->getId() . '" class="'
	                            . $class . ' product-custom-option" name="options[' . $_option->getId() . ']"'
	                            . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"')
	                            . ' value="" checked="checked" /><span class="label"><label for="options_'
	                            . $_option->getId() . '">' . $this->__('None') . '</label></span></li>';
	                    	}
	                        
	                    }
	                    break;
	                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX:
	                    $type = 'checkbox';
	                    $class = 'checkbox';
	                    $arraySign = '[]';
	                    break;
	            }
	            $count = 1;
	            foreach ($_option->getValues() as $_value) {
	                $count++;
	
	                $priceStr = $this->_formatPrice(array(
	                    'is_percent'    => ($_value->getPriceType() == 'percent'),
	                    'pricing_type'=> $_value->getPriceType(),
	                    'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent')
	                ));
	
	                $htmlValue = $_value->getOptionTypeId();
	                if ($arraySign) {
	                    $checked = (is_array($configValue) && in_array($htmlValue, $configValue)) ? 'checked' : '';
	                } else {
	                    $checked = $configValue == $htmlValue ? 'checked' : '';
	                }
	            	// them code vao --------------------
	            	
	           	    $mw_class = 'mw_class_'.$_option->getId();
	           	    $image_size_config = Mage::getStoreConfig('advancedproductoption/config/image_size');
					$array_image_size_config = explode(',',$image_size_config);	
					$width_config = 80;
					$height_config = 80;
					if(isset($array_image_size_config[0])) $width_config = $array_image_size_config[0];
					if(isset($array_image_size_config[1])) $height_config = $array_image_size_config[1];
					$option_id = $_option->getId();
					$option_type_id = "mw_image_select_".$option_id.'_'.$_value ->getOptionTypeId();
					$class_option = "mw_class_image_select_".$option_id;
					$image_name = $_value ->getMwImage();
					$image = '';
					if($image_name != '')
					{	
						$width = $_value->getMwImageSizeX();
						$height = $_value->getMwImageSizeY();
						$mw_title = $_value->getTitle();
						if($width == 0 || $height == 0) {
							$width = (int)$width_config;
							$height = (int)$height_config;
						}
						$image_url = Mage::helper('advancedproductoption/image')->init($image_name)->keepAspectRatio(false)->constrainOnly(false)->keepFrame(false)->resize($width,$height);
						$image = '<img id ="'.$option_type_id.'" class ="'.$class_option.'" style="vertical-align: middle;margin-left: 10px; margin-top: 10px;" onclick="mw_selectOptionType.clickImage(this.id);" title="'.$mw_title.'" alt="'.$mw_title.'" src="'.$image_url.'"  width="'.$width.'" height="'.$height.'" />';
					}
						
	                if($enabled_module){
		                $mw_qty_show = '';
				    	$mw_condition_qty = $enabled_module;
				        if($enabled_qty && $_option->getMwQtyInput()){
				        	$mw_qty_show = ' ('.$_value->getMwQty().')';
				        	$mw_condition_qty = $_value->getMwQty()>0;
				        }
	                	$mw_qty = '';
	                	if($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX && $enabled_qty && $_option->getMwQtyInput())
	                	$mw_qty ='<span class="qty-holder"><label>Qty: <input type="text" onkeypress="if(event.keyCode==13){opConfig.reloadPrice(); }" onchange="opConfig.reloadPrice(); " name="mw_options_'
			                     . $_option->getId().'_'.$_value ->getOptionTypeId().'_qty" id="mw_options_'.$_option->getId().'_'.$_value ->getOptionTypeId().'_qty" maxlength="12" value="1" class="input-text qty validate-greater-than-zero" /></label></span>';
	                	
			           if($method_show_image == MW_Advancedproductoption_Model_Method::NORMAL){
	                		  if($mw_condition_qty)
	                		  $selectHtml .= '<li>' . '<input type="' . $type . '" class="' . $class . ' ' . $require
			                    . ' product-custom-option"'
			                    . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();mw_selectOptionType.showImage(this.id);"')
			                    . ' name="options[' . $_option->getId() . ']' . $arraySign . '" id="options_' . $_option->getId()
			                    . '_' . $count . '" value="' . $htmlValue . '" ' . $checked . ' price="'
			                    . $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />'
			                    . '<span class="label"><label for="options_' . $_option->getId() . '_' . $count . '">'
			                    . $_value->getTitle() .$mw_qty_show. ' ' . $priceStr . '</label></span>'.$mw_qty;
	                	} 
			            else if($method_show_image == MW_Advancedproductoption_Model_Method::CLICKIMAGE){
			            	if($mw_condition_qty)
			            	 $selectHtml .= '<li class="mw_item">' .$image. '<div><input type="' . $type . '" class="'.$mw_class.' '.$class . ' ' . $require
			                    . ' product-custom-option"'
			                    . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice();mw_selectOptionType.showImageNew(this.id);"')
			                    . ' name="options[' . $_option->getId() . ']' . $arraySign . '" id="options_' . $_option->getId()
			                    . '_' . $count . '" value="' . $htmlValue . '" ' . $checked . ' price="'
			                    . $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />'
			                    . '<span class="label"><label for="options_' . $_option->getId() . '_' . $count . '">'
			                    . $_value->getTitle() . $mw_qty_show.' ' . $priceStr . '</label></span>'.$mw_qty.'</div>';
			            
			            
			            }
	                   
	                }else{
	                	 $selectHtml .= '<li>' . '<input type="' . $type . '" class="' . $class . ' ' . $require
		                    . ' product-custom-option"'
		                    . ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"')
		                    . ' name="options[' . $_option->getId() . ']' . $arraySign . '" id="options_' . $_option->getId()
		                    . '_' . $count . '" value="' . $htmlValue . '" ' . $checked . ' price="'
		                    . $this->helper('core')->currencyByStore($_value->getPrice(true), $store, false) . '" />'
		                    . '<span class="label"><label for="options_' . $_option->getId() . '_' . $count . '">'
		                    . $_value->getTitle() . ' ' . $priceStr . '</label></span>';
	                }
	                
	               
	                    
	                if ($_option->getIsRequire()) {
	                    $selectHtml .= '<script type="text/javascript">' . '$(\'options_' . $_option->getId() . '_'
	                    . $count . '\').advaiceContainer = \'options-' . $_option->getId() . '-container\';'
	                    . '$(\'options_' . $_option->getId() . '_' . $count
	                    . '\').callbackFunction = \'validateOptionsCallback\';' . '</script>';
	                }
	                $selectHtml .= '</li>';
	            }
	            $selectHtml .= '</ul>';
	
	            return $selectHtml;
	        }
	 		
	 		// ket thuc code dung cho ban 1700--------------------------	
	 	}             
	        
    }
    
	protected function _formatPrice($value, $flag=true)
    {
        if ($value['pricing_value'] == 0) {
            return '';
        }

        $taxHelper = Mage::helper('tax');
        $store = $this->getProduct()->getStore();

        $sign = '+';
        if($value['pricing_type'] == 'abs') $sign = '';
        if ($value['pricing_value'] < 0) {
            $sign = '-';
            $value['pricing_value'] = 0 - $value['pricing_value'];
        }

        $priceStr = $sign;
        $_priceInclTax = $this->getPrice($value['pricing_value'], true);
        $_priceExclTax = $this->getPrice($value['pricing_value']);
        if ($taxHelper->displayPriceIncludingTax()) {
            $priceStr .= $this->helper('core')->currencyByStore($_priceInclTax, $store, true, $flag);
        } elseif ($taxHelper->displayPriceExcludingTax()) {
            $priceStr .= $this->helper('core')->currencyByStore($_priceExclTax, $store, true, $flag);
        } elseif ($taxHelper->displayBothPrices()) {
            $priceStr .= $this->helper('core')->currencyByStore($_priceExclTax, $store, true, $flag);
            if ($_priceInclTax != $_priceExclTax) {
                $priceStr .= ' ('.$sign.$this->helper('core')
                    ->currencyByStore($_priceInclTax, $store, true, $flag).' '.$this->__('Incl. Tax').')';
            }
        }

        if ($flag) {
            $priceStr = '<span class="price-notice">'.$priceStr.'</span>';
        }

        return $priceStr;
    }
    
    
    

}
