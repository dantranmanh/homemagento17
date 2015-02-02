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
 * Catalog product controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';
class  MW_Advancedproductoption_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
	public function setMwCustomOptionSku($product_id, $product_id_from)
    {
    	$storeId = Mage::app()->getStore()->getId();
    	$optionDatas = Mage::getResourceModel('catalog/product_option_collection')
										->getOptions($storeId) // store
										->addFieldToFilter('product_id',$product_id_from)
										->addValuesToResult($storeId);
		//$model_catalog = Mage::getModel('catalog/product');		
		//zend_debug::dump($optionDatas->getData());die();						
        foreach ($optionDatas as $optionData) {
        	//zend_debug::dump($optionData->getValues());die();
            if ($optionData->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
            	
				$optionData_new = array();
				$optionData_new  = $optionData->getData();
				unset($optionData_new['product_id']);
				$optionData_new['product_id'] = (int)$product_id;
				unset($optionData_new['option_id']);
				
				$mw_value = array();
				foreach ($optionData->getValues() as $optionDatavalues) {
					$optionDatavalues->unsetData('option_id');
					$optionDatavalues->unsetData('option_type_id');
					$mw_value[] = $optionDatavalues->getData();
				}
				$mw_data = array();
				$mw_data = array_merge($optionData_new,array('values'=> $mw_value));
				
				$product_new = Mage::getModel('catalog/product')->load($product_id);
		    	$product_new ->setId($product_id)->setHasOptions(1)->save();
		        $option = Mage::getModel('catalog/product_option')
					        		  ->setData($mw_data)
					                  ->setProduct($product_new);
		        $option->save();
		        
			}else{
				
				$optionData_other = $optionData->getData();
				unset($optionData_other['option_id']);
				unset($optionData_new['product_id']);
            	$optionData_other['product_id'] = (int)$product_id;
		   
		    	$product_new = Mage::getModel('catalog/product')->load($product_id);
		    	$product_new ->setId($product_id)->setHasOptions(1)->save();
		        $option = Mage::getModel('catalog/product_option')
					        		  ->setData($optionData_other)
					                  ->setProduct($product_new);
		        $option->save();
			}   
        }  
    }
	public function setMwCustomOption($product_id, $template_id)
    {
    	$optionDatas = Mage::getResourceModel('advancedproductoption/product_option_collection')
										->getOptions(0) // store
										->addFieldToFilter('template_id',$template_id)
										->addValuesToResult(0);
		//$model_catalog = Mage::getModel('catalog/product');								
        foreach ($optionDatas as $optionData) {
            
            if ($optionData->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
            	
				$optionData_new = array();
				$optionData_new  = $optionData->getData();
				$optionData_new['product_id'] = (int)$product_id;
				unset($optionData_new['option_id']);
				
				$mw_value = array();
				foreach ($optionData->getValues() as $optionDatavalues) {
					$optionDatavalues->unsetData('option_id');
					$optionDatavalues->unsetData('option_type_id');
					$mw_value[] = $optionDatavalues->getData();
				}
				$mw_data = array();
				$mw_data = array_merge($optionData_new,array('values'=> $mw_value));
				
				$product_new = Mage::getModel('catalog/product')->load($product_id);
		    	$product_new ->setId($product_id)->setHasOptions(1)->save();
		        $option = Mage::getModel('catalog/product_option')
					        		  ->setData($mw_data)
					                  ->setProduct($product_new);
		        $option->save();
		        
			}else{
				
				$optionData_other = $optionData->getData();
				unset($optionData_other['option_id']);
            	$optionData_other['product_id'] = (int)$product_id;
		    	
		    	$product_new = Mage::getModel('catalog/product')->load($product_id);
		    	$product_new ->setId($product_id)->setHasOptions(1)->save();
		        $option = Mage::getModel('catalog/product_option')
					        		  ->setData($optionData_other)
					                  ->setProduct($product_new);
		        $option->save();
			}   
        }  
    }
    
    public function saveAction()
    {
    	$enabled_module = Mage::getStoreConfig('advancedproductoption/config/enabled');
    	if($enabled_module){
    	//echo 'rewrite successfully';die();
	        $storeId        = $this->getRequest()->getParam('store');
	        $redirectBack   = $this->getRequest()->getParam('back', false);
	        $productId      = $this->getRequest()->getParam('id');
	        $isEdit         = (int)($this->getRequest()->getParam('id') != null);
	
	        $data = $this->getRequest()->getPost();
	        //zend_debug::dump($data);die();
	        if ($data) {
	            if (!isset($data['product']['stock_data']['use_config_manage_stock'])) {
	                $data['product']['stock_data']['use_config_manage_stock'] = 0;
	            }
	            $product = $this->_initProductSave();
	
	            try {
	            	$product ->setMwData($this->getRequest()->getPost());
	                $product->save();
	                $productId = $product->getId();
	                
	                //---------------------them code vao----------------------------------------
	                
	                $template_id = $this->getRequest()->getPost('mw_template_id');
	                if(isset($template_id)) $this ->setMwCustomOption($productId, $template_id);
	                $mw_sku = $this->getRequest()->getPost('mw_sku');
	                if(isset($mw_sku)){
	                	$mw_product_id = Mage::getModel('catalog/product')->getIdBySku($mw_sku);
	                	if($mw_product_id && $mw_product_id != $productId) $this ->setMwCustomOptionSku($productId, $mw_product_id);
	                } 
	                
	                //-----------------ket thuc them code vao -------------------------------------
	               
	
	                /**
	                 * Do copying data to stores
	                 */
	                if (isset($data['copy_to_stores'])) {
	                    foreach ($data['copy_to_stores'] as $storeTo=>$storeFrom) {
	                        $newProduct = Mage::getModel('catalog/product')
	                            ->setStoreId($storeFrom)
	                            ->load($productId)
	                            ->setStoreId($storeTo)
	                            ->save();
	                    }
	                }
	
	                Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($productId);
	
	                $this->_getSession()->addSuccess($this->__('The product has been saved.'));
	            }
	            catch (Mage_Core_Exception $e) {
	                $this->_getSession()->addError($e->getMessage())
	                    ->setProductData($data);
	                $redirectBack = true;
	            }
	            catch (Exception $e) {
	                Mage::logException($e);
	                $this->_getSession()->addError($e->getMessage());
	                $redirectBack = true;
	            }
	        }
	
	        if ($redirectBack) {
	            $this->_redirect('*/*/edit', array(
	                'id'    => $productId,
	                '_current'=>true
	            ));
	        }
	        else if($this->getRequest()->getParam('popup')) {
	            $this->_redirect('*/*/created', array(
	                '_current'   => true,
	                'id'         => $productId,
	                'edit'       => $isEdit
	            ));
	        }
	        else {
	            $this->_redirect('*/*/', array('store'=>$storeId));
	        }
    	}else{
    		parent::saveAction();
    	}
    }
	public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $product = Mage::getModel('catalog/product')
                ->load($id);
            $sku = $product->getSku();
            try {
            	$this ->mwDeleteImage($id);
                $product->delete();
                $this->_getSession()->addSuccess($this->__('The product has been deleted.'));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
    }
    
	public function massDeleteAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        }
        else {
            try {
                foreach ($productIds as $productId) {
                	$this ->mwDeleteImage($productId);
                    $product = Mage::getSingleton('catalog/product')->load($productId);
                    Mage::dispatchEvent('catalog_controller_product_delete', array('product' => $product));
                    $product->delete();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($productIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
	public function mwCheckDeleteImage($image_name,$option_id)
    {
    	$collection_image_text = Mage::getModel('catalog/product_option')->getCollection()
        								->addFieldtoFilter('mw_image',$image_name)
        								->addFieldtoFilter('option_id', array('neq' => $option_id));
    	$collection_image_select = Mage::getModel('catalog/product_option_value')->getCollection()
        								->addFieldtoFilter('mw_image',$image_name)
        								->addFieldtoFilter('option_id', array('neq' => $option_id));
        $template_image_text = Mage::getModel('advancedproductoption/product_option')->getCollection()
        													->addFieldtoFilter('mw_image',$image_name);
    	$template_image_select = Mage::getModel('advancedproductoption/product_option_value')->getCollection()
        										  					->addFieldtoFilter('mw_image',$image_name);
        if(sizeof($collection_image_text)==0 && sizeof($collection_image_select)==0 && sizeof($template_image_text)==0 && sizeof($template_image_select)==0) 
        	return true;
        else return false;
        
    	
    }
    
    public function mwDeleteImage($id)
    {
    	// xoa du lieu trong cac bang option template
		$collection_options = Mage::getModel('catalog/product_option')->getCollection()
												  ->addFieldtoFilter('product_id',$id);
		if(sizeof($collection_options)>0){
			foreach ($collection_options as $collection_option) {
				$option_id = $collection_option->getOptionId();
				// xoa image kieu text
				$image_old_text = '';
        		$image_old_text = Mage::getModel('catalog/product_option') ->load($option_id)->getMwImage();
        		if($image_old_text != ''&& substr_count($image_old_text,'mw_product') > 0 ) @unlink(Mage::getBaseDir('media') . DS.$image_old_text);
				if($image_old_text != '' && substr_count($image_old_text,'mw_product')== 0 ){
        			if($this ->mwCheckDeleteImage($image_old_text,$option_id)) @unlink(Mage::getBaseDir('media') . DS.$image_old_text);
        		}
        		
        		// xoa image kieu select
				$collection_image_delete_selects = Mage::getModel('catalog/product_option_value')->getCollection()
        									   							->addFieldtoFilter('option_id',$option_id);
        		if(sizeof($collection_image_delete_selects) > 0){
        			foreach ($collection_image_delete_selects as $collection_image_delete_select) {
        				$image_select_delete = $collection_image_delete_select ->getMwImage();
        				if($image_select_delete != '' && substr_count($image_select_delete,'mw_product') > 0 ) @unlink(Mage::getBaseDir('media') . DS.$image_select_delete);
	        			if($image_select_delete != '' && substr_count($image_select_delete,'mw_product')== 0 ){
		        			if($this ->mwCheckDeleteImage($image_select_delete,$option_id)) @unlink(Mage::getBaseDir('media') . DS.$image_select_delete);
		        		}
        			}
        		}
			}
		}
    	
    }
    
    
    
}
