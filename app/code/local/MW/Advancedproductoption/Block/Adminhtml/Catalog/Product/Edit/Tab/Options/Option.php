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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * customers defined options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class MW_Advancedproductoption_Block_Adminhtml_Catalog_Product_Edit_Tab_Options_Option extends Mage_Adminhtml_Block_Widget
{
    protected $_product;

    protected $_productInstance;

    protected $_values;

    protected $_itemCount = 1;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mw_advancedproductoption/mage/catalog/product/edit/options/option.phtml');
    }
	
    public function getItemCount()
    {
        return $this->_itemCount;
    }

    public function setItemCount($itemCount)
    {
        $this->_itemCount = max($this->_itemCount, $itemCount);
        return $this;
    }


    public function getProduct()
    {
        if (!$this->_productInstance) {
            if ($product = Mage::registry('product')) {
                $this->_productInstance = $product;
            } else {
                $this->_productInstance = Mage::getSingleton('catalog/product');
            }
        }

        return $this->_productInstance;
    }

    public function setProduct($product)
    {
        $this->_productInstance = $product;
        return $this;
    }

    
    public function getFieldName()
    {
        return 'product[options]';
    }

    
    public function getFieldId()
    {
        return 'product_option';
    }

    
    public function isReadonly()
    {
         return $this->getProduct()->getOptionsReadonly();
    }
 
    protected function _prepareLayout()
    {
        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('catalog')->__('Delete Option'),
                    'class' => 'delete delete-product-option '
                ))
        );

        /*$path = 'global/catalog/product/options/custom/groups'; 

        foreach (Mage::getConfig()->getNode($path)->children() as $group) {
            $this->setChild($group->getName() . '_option_type',
                $this->getLayout()->createBlock(
                    (string) Mage::getConfig()->getNode($path . '/' . $group->getName() . '/render')
                )
            );
        }*/
        
        
        // them code vao
         $this->setChild('text_option_type',
            $this->getLayout()->createBlock('advancedproductoption/adminhtml_catalog_product_edit_tab_options_type_text')
        );
        $this->setChild('file_option_type',
            $this->getLayout()->createBlock('advancedproductoption/adminhtml_catalog_product_edit_tab_options_type_file')
        );
        $this->setChild('select_option_type',
            $this->getLayout()->createBlock('advancedproductoption/adminhtml_catalog_product_edit_tab_options_type_select')
        );
        $this->setChild('date_option_type',
            $this->getLayout()->createBlock('advancedproductoption/adminhtml_catalog_product_edit_tab_options_type_date')
        );
        // ket thuc them code vao

        return parent::_prepareLayout();
    }

    public function getAddButtonId()
    {
        $buttonId = $this->getLayout()
                ->getBlock('admin.product.options')
                //->getBlock('advancedproductoption/adminhtml_catalog_product_edit_tab_options')
                ->getChild('add_button')->getId();
        return $buttonId;
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setData(array(
                'id' => $this->getFieldId().'_{{id}}_type',
                'class' => 'select select-product-option-type required-option-select'
            ))
            ->setName($this->getFieldName().'[{{id}}][type]')
            ->setOptions(Mage::getSingleton('adminhtml/system_config_source_product_options_type')->toOptionArray());

        return $select->getHtml();
    }

    public function getRequireSelectHtml()
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setData(array(
                'id' => $this->getFieldId().'_{{id}}_is_require',
                'class' => 'select'
            ))
            ->setName($this->getFieldName().'[{{id}}][is_require]')
            ->setOptions(Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray());

        return $select->getHtml();
    }
	public function getMwQtySelectHtml()
    {
        $select = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setData(array(
                'id' => $this->getFieldId().'_{{id}}_mw_qty_input',
                'class' => 'select'
            ))
            ->setName($this->getFieldName().'[{{id}}][mw_qty_input]')
            ->setOptions(Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray());

        return $select->getHtml();
    }

    public function getTemplatesHtml()
    {
        $templates = $this->getChildHtml('text_option_type') . "\n" .
            $this->getChildHtml('file_option_type') . "\n" .
            $this->getChildHtml('select_option_type') . "\n" .
            $this->getChildHtml('date_option_type');

        return $templates;
    }

    public function getOptionValues()
    {
        $optionsArr = array_reverse($this->getProduct()->getOptions(), true);

        if (!$this->_values) {
            $values = array();
            $scope = (int) Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);
            foreach ($optionsArr as $option) {
                

                $this->setItemCount($option->getOptionId());

                $value = array();

                $value['id'] = $option->getOptionId();
                $value['item_count'] = $this->getItemCount();
                $value['option_id'] = $option->getOptionId();
                $value['title'] = $this->htmlEscape($option->getTitle());
                $value['type'] = $option->getType();
                $value['is_require'] = $option->getIsRequire();
                $value['sort_order'] = $option->getSortOrder();
                $value['mw_description'] = $option->getMwDescription();
                $value['mw_customer_groups'] = $option->getMwCustomerGroups();
                $value['mw_qty_input'] = $option->getMwQtyInput();

                if ($this->getProduct()->getStoreId() != '0') {
                    $value['checkboxScopeTitle'] = $this->getCheckboxScopeHtml($option->getOptionId(), 'title', is_null($option->getStoreTitle()));
                    $value['scopeTitleDisabled'] = is_null($option->getStoreTitle())?'disabled':null;
                }

                if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {

//                    $valuesArr = array_reverse($option->getValues(), true);

                    $i = 0;
                    $itemCount = 0;
                    foreach ($option->getValues() as $_value) {
                    	$image_name = $_value ->getMwImage();
                    	//$image_name = str_replace('\\','/',$image_name);
                    	//$image_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$image_name; 
                		//$image = '<img style="vertical-align: middle;" src="'.$image_url.'" width="60" height="60" />';
                    	$image = '<img style="vertical-align: middle;" src="'.Mage::helper('advancedproductoption/image')->init($image_name)->keepAspectRatio(false)->constrainOnly(false)->keepFrame(false)->resize(60,60).'" />';
                    	
                    	if($image_name !='')
                    	{
                    		$image_button_label = 'Change Image';
                    		$image_new = $image.'<input type="checkbox" style=" margin-left: 5px;"  name="delete_image'.$_value->getOptionTypeId().'" /> <span style="">Delete Image</span>';
                    	}
                    	else {
                    		$image_new = '';
                    		$image_button_label = 'Add Image';
                    	}
                    	
                        
                        $value['optionValues'][$i] = array(
                            'item_count' => max($itemCount, $_value->getOptionTypeId()),
                            'option_id' => $_value->getOptionId(),
                            'option_type_id' => $_value->getOptionTypeId(),
                            'title' => $this->htmlEscape($_value->getTitle()),
                            'price' => $this->getPriceValue($_value->getPrice(), $_value->getPriceType()),
                            'price_type' => $_value->getPriceType(),
                            'sku' => $this->htmlEscape($_value->getSku()),
                            'sort_order' => $_value->getSortOrder(),
                        	'mw_qty' => $_value ->getMwQty(),
	                        'mw_image_size_x' => $_value ->getMwImageSizeX(),
	                        'mw_image_size_y' => $_value ->getMwImageSizeY(),
                        	'mw_image' => $image_new,
                        	'image_button_label' => $image_button_label,
                        				
                        );

                        if ($this->getProduct()->getStoreId() != '0') {
                            $value['optionValues'][$i]['checkboxScopeTitle'] = $this->getCheckboxScopeHtml($_value->getOptionId(), 'title', is_null($_value->getStoreTitle()), $_value->getOptionTypeId());
                            $value['optionValues'][$i]['scopeTitleDisabled'] = is_null($_value->getStoreTitle())?'disabled':null;
                            if ($scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE) {
                                $value['optionValues'][$i]['checkboxScopePrice'] = $this->getCheckboxScopeHtml($_value->getOptionId(), 'price', is_null($_value->getstorePrice()), $_value->getOptionTypeId());
                                $value['optionValues'][$i]['scopePriceDisabled'] = is_null($_value->getStorePrice())?'disabled':null;
                            }
                        }
                        $i++;
                    }
                } else {
                	$image_name = $option ->getMwImage();
                    //$image_name = str_replace('\\','/',$image_name);
                	//$image_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$image_name; 
                	//$image = '<img style="vertical-align: middle;" src="'.$image_url.'" width="60" height="60" />';
               		$image = '<img style="vertical-align: middle;" src="'.Mage::helper('advancedproductoption/image')->init($image_name)->keepAspectRatio(false)->constrainOnly(false)->keepFrame(false)->resize(60,60).'" />';
               		if($image_name !='')
                    {
                    	$image_button_label = 'Change Image';
                    	$image_new = $image.'<input type="checkbox" style=" margin-left: 5px;"  name="delete_image_text'.$option->getOptionId().'" /> <span style="">Delete Image</span>';
                    }
                    else {
                    	$image_new = '';
                    	$image_button_label = 'Add Image';
                    }
                    	
                    $value['price'] = $this->getPriceValue($option->getPrice(), $option->getPriceType());
                    $value['price_type'] = $option->getPriceType();
                    $value['sku'] = $this->htmlEscape($option->getSku());
                    $value['max_characters'] = $option->getMaxCharacters();
                    $value['file_extension'] = $option->getFileExtension();
                    $value['image_size_x'] = $option->getImageSizeX();
                    $value['image_size_y'] = $option->getImageSizeY();
                    $value['mw_image_size_x'] = $option->getMwImageSizeX();
                    $value['mw_image_size_y'] = $option->getMwImageSizeY();
                    $value['mw_image'] = $image_new;
                    $value['image_button_label'] = $image_button_label;
                    if ($this->getProduct()->getStoreId() != '0' && $scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE) {
                        $value['checkboxScopePrice'] = $this->getCheckboxScopeHtml($option->getOptionId(), 'price', is_null($option->getStorePrice()));
                        $value['scopePriceDisabled'] = is_null($option->getStorePrice())?'disabled':null;
                    }
                }
                $values[] = new Varien_Object($value);
            }
            $this->_values = $values;
        }

        return $this->_values;
    }

    public function getCheckboxScopeHtml($id, $name, $checked=true, $select_id='-1')
    {
        $checkedHtml = '';
        if ($checked) {
            $checkedHtml = ' checked="checked"';
        }
        $selectNameHtml = '';
        $selectIdHtml = '';
        if ($select_id != '-1') {
            $selectNameHtml = '[values]['.$select_id.']';
            $selectIdHtml = 'select_'.$select_id.'_';
        }
        $checkbox = '<input type="checkbox" id="'.$this->getFieldId().'_'.$id.'_'.$selectIdHtml.$name.'_use_default" class="product-option-scope-checkbox" name="'.$this->getFieldName().'['.$id.']'.$selectNameHtml.'[scope]['.$name.']" value="1" '.$checkedHtml.'/>';
        $checkbox .= '<label class="normal" for="'.$this->getFieldId().'_'.$id.'_'.$selectIdHtml.$name.'_use_default">Use Default Value</label>';
        return $checkbox;
    }

    public function getPriceValue($value, $type)
    {
        if ($type == 'percent') {
            return number_format($value, 2, null, '');
        } elseif ($type == 'fixed' || $type == 'abs' || $type == 'onetime') {
            return number_format($value, 2, null, '');
        }
    }
}
