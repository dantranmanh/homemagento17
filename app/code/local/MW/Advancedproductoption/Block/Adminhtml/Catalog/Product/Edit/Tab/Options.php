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

//class MW_Advancedproductoption_Block_Adminhtml_Catalog_Product_Edit_Tab_Options extends Mage_Adminhtml_Block_Widget
class MW_Advancedproductoption_Block_Adminhtml_Catalog_Product_Edit_Tab_Options extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options
{
    public function __construct()
    {
        //parent::__construct();
        $enabled_module = Mage::getStoreConfig('advancedproductoption/config/enabled');
        if($enabled_module)$this->setTemplate('mw_advancedproductoption/mage/catalog/product/edit/options.phtml');
        else{
        	parent::__construct();
        }
    }


    protected function _prepareLayout()
    {
    	$enabled_module = Mage::getStoreConfig('advancedproductoption/config/enabled');
    	if($enabled_module){
	        $this->setChild('add_button',
	            $this->getLayout()->createBlock('adminhtml/widget_button')
	                ->setData(array(
	                    'label' => Mage::helper('catalog')->__('Add New Option'),
	                    'class' => 'add',
	                    'id'    => 'add_new_defined_option'
	                ))
	        );
	        $this->setChild('options_box',
	            $this->getLayout()->createBlock('advancedproductoption/adminhtml_catalog_product_edit_tab_options_option')
	        );
	        $this->setChild('options_template',
	            $this->getLayout()->createBlock('advancedproductoption/adminhtml_catalog_product_edit_tab_template')
	        );
	
	       // return parent::_prepareLayout();
    	}else return parent::_prepareLayout();
    }
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }
	public function getOptionsTemplateHtml()
    {
        return $this->getChildHtml('options_template');
    }

}
