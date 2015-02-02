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
 * Catalog product option model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class MW_Advancedproductoption_Model_Mage_Product_Option extends Mage_Catalog_Model_Product_Option
{
    /**
     * Save options.
     *
     * @return Mage_Catalog_Model_Product_Option
     */
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
    public function saveOptions()
    {
    	$data = $this->getProduct()->getMwData();
        foreach ($this->getOptions() as $option) {
        	// them customer groups cho tung option
        	if(isset($option['mw_customer_groups'])){
        		$mw_customer_groups = $option['mw_customer_groups'];
				//zend_debug::dump($stores);die();
				$mw_customer_count = count($mw_customer_groups);
				$mw_customer_index = 1;
				$mw_customer_data = '';
				foreach ($mw_customer_groups as $mw_customer_group){
					$mw_customer_data .= $mw_customer_group;
					if ($mw_customer_index < $mw_customer_count){
						$mw_customer_data .= ',';
					}
					$mw_customer_index++;
				}
				 $option['mw_customer_groups'] = $mw_customer_data;
        		
        	}else{
        		$option['mw_customer_groups'] = '';
        	}
        	// them code vao voi trg hop la filed-------------------------------------
        	$option_id = 0;
        	if(isset($option['option_id'])) $option_id = $option['option_id'];
        	// xoa image neu ton tai is_delete
        	if($option_id && $option['is_delete'] == '1'){
        		// xoa image kieu text
        		$image_text_delete = '';
        		$image_text_delete = Mage::getModel('catalog/product_option') ->load($option_id)->getMwImage();
        		if($image_text_delete != '' && substr_count($image_text_delete,'mw_product') > 0 ) @unlink(Mage::getBaseDir('media') . DS.$image_text_delete);
        		if($image_text_delete != '' && substr_count($image_text_delete,'mw_product')== 0 ){
        			if($this ->mwCheckDeleteImage($image_text_delete,$option_id)) @unlink(Mage::getBaseDir('media') . DS.$image_text_delete);
        		}
        		// xoa image kieu select
        		$collection_image_delete_selects = Mage::getModel('catalog/product_option_value')->getCollection()
        										   ->addFieldtoFilter('option_id',$option_id);
        		if(sizeof($collection_image_delete_selects) > 0){
        			foreach ($collection_image_delete_selects as $collection_image_delete_select) {
        				$image_select_delete = $collection_image_delete_select ->getMwImage();
        				if($image_select_delete != '' && substr_count($image_select_delete,'mw_product') > 0) @unlink(Mage::getBaseDir('media') . DS.$image_select_delete);
	        			if($image_select_delete != '' && substr_count($image_select_delete,'mw_product')== 0 ){
	        				if($this ->mwCheckDeleteImage($image_select_delete,$option_id)) @unlink(Mage::getBaseDir('media') . DS.$image_select_delete);
	        			}
        			}
        		}
        	}
        	// code xoa image, cap nhat lai colum image
        	if($option_id){
	        	$delete_image_name_text = 'delete_image_text'.$option_id;
	        	if(isset($data[$delete_image_name_text]) && $data[$delete_image_name_text] == 'on'){
	        		$option['mw_image'] = '';
	        		// code xoa image cu
	        		$image_old_text = '';
	        		$image_old_text = Mage::getModel('catalog/product_option') ->load($option_id)->getMwImage();
	        		if($image_old_text != '' && substr_count($image_old_text,'mw_product') > 0 ) @unlink(Mage::getBaseDir('media') . DS.$image_old_text);
	        		if($image_old_text != '' && substr_count($image_old_text,'mw_product')== 0 ){
        				if($this ->mwCheckDeleteImage($image_old_text,$option_id)) @unlink(Mage::getBaseDir('media') . DS.$image_old_text);
        			}
	        	}
        	}
        	$id = -999;
        	if(isset($option['id']))
        	{
	        	$id = $option['id'];
	        	$name_image_text = 'file_text_'.$id;
	        	if(isset($_FILES[$name_image_text]['name'])){	
		        	if(isset($_FILES[$name_image_text]['name']) && $_FILES[$name_image_text]['name'] != '') {
						try {	
							
							$uploader = new Varien_File_Uploader($name_image_text);
							
							// Any extention would work
			           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
							$uploader->setAllowRenameFiles(true);
							
							$uploader->setFilesDispersion(true);
									
							// We set media as the upload dir
							$file_name = $uploader->getCorrectFileName($_FILES[$name_image_text]['name']);
							$path = Mage::getBaseDir('media') . DS."mw_advancedproductoption".DS."mw_product";
							$image_file_name_new = 'text_'.$id.'_'.time().'_'.$file_name;
							$uploader->save($path, $image_file_name_new );
							
							$path_new = $uploader->getDispretionPath($image_file_name_new);
							//$option['mw_image'] = "mw_advancedproductoption".DS."mw_product".$path_new.DS.$image_file_name_new;
							$option['mw_image'] = "mw_advancedproductoption".DS."mw_product".$uploader->getUploadedFileName();
							// code xoa image cu
		        			$image_old_text = '';
		        			if($option_id){
			        			$image_old_text = Mage::getModel('catalog/product_option')->load($id)->getMwImage();
			        			$option_id = $id;
			        			if($image_old_text != '' && substr_count($image_old_text,'mw_product') > 0 ) @unlink(Mage::getBaseDir('media') . DS.$image_old_text);
								if($image_old_text != '' && substr_count($image_old_text,'mw_product')== 0 ){
			        				if($this ->mwCheckDeleteImage($image_old_text,$option_id)) @unlink(Mage::getBaseDir('media') . DS.$image_old_text);
			        			}
		        			}
						
						} catch (Exception $e) {
				      
				        }
				        
					}		 
	        	}
        	}
        	
       		// them code vao voi trg hop la select box----------------------------------
        	if(isset($option['values'])){
        		//zend_debug::dump($option['values']);
       			//zend_debug::dump($_FILES);die();
	        	foreach ($option['values'] as $key => $value) {
	        		if(isset($key)){
		        		// code xoa image, cap nhat lai colum image
		        		$delete_image_name = 'delete_image'.$key;
		        		if(isset($data[$delete_image_name]) && $data[$delete_image_name] == 'on'){ 
		        			$option['values'][$key]['mw_image'] = '';
		        			// code xoa image cu
		        			$image_old = '';
		        			$image_old = Mage::getModel('catalog/product_option_value') ->load($key)->getMwImage();
		        			$option_id = Mage::getModel('catalog/product_option_value') ->load($key)->getOptionId();
		        			if($image_old != '' && substr_count($image_old,'mw_product') > 0 ) @unlink(Mage::getBaseDir('media') . DS.$image_old);
			        		if($image_old != '' && substr_count($image_old,'mw_product')== 0 ){
		        				if($this ->mwCheckDeleteImage($image_old,$option_id)) @unlink(Mage::getBaseDir('media') . DS.$image_old);
		        			}
		        		}
		        		if($option['values'][$key]['is_delete'] == '1' && $option['values'][$key]['option_type_id'] != '-1'){
		        			$image_delete_select = '';
		        			$image_delete_select = Mage::getModel('catalog/product_option_value') ->load($key)->getMwImage();
		        			$option_id = Mage::getModel('catalog/product_option_value') ->load($key)->getOptionId();
		        			if($image_delete_select != '' && substr_count($image_delete_select,'mw_product') > 0 ) @unlink(Mage::getBaseDir('media') . DS.$image_delete_select);
		        			if($image_delete_select != '' && substr_count($image_delete_select,'mw_product')== 0 ){
		        				if($this ->mwCheckDeleteImage($image_delete_select,$option_id)) @unlink(Mage::getBaseDir('media') . DS.$image_delete_select);
		        			}
		        		}
		        		$name_image = 'file_'.$key;
		        		//zend_debug::dump($_FILES[$name_image]['name']);die();
		        		if(isset($_FILES[$name_image]['name'])){	
				        	if(isset($_FILES[$name_image]['name']) && $_FILES[$name_image]['name'] != '') {
								try {	
									
									$uploader = new Varien_File_Uploader($name_image);
									
									// Any extention would work
					           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
									$uploader->setAllowRenameFiles(true);
									
									$uploader->setFilesDispersion(true);
											
									// We set media as the upload dir
									$file_name = $uploader->getCorrectFileName($_FILES[$name_image]['name']);
									$path = Mage::getBaseDir('media') . DS."mw_advancedproductoption".DS."mw_product";
									$image_file_name_new = 'select_'.$key.'_'.time().'_'.$file_name;
									$uploader->save($path, $image_file_name_new );
									
									$path_new = $uploader->getDispretionPath($image_file_name_new);
									//$option['values'][$key]['mw_image'] = "mw_advancedproductoption".DS."mw_product".$path_new.DS.$image_file_name_new;
									$option['values'][$key]['mw_image'] = "mw_advancedproductoption".DS."mw_product".$uploader->getUploadedFileName();
									
									// code xoa image cu
				        			$image_old = '';
				        			if($option['values'][$key]['option_type_id'] != '-1'){
					        			$image_old = Mage::getModel('catalog/product_option_value')->load($key)->getMwImage();
					        			$option_id = Mage::getModel('catalog/product_option_value') ->load($key)->getOptionId();
					        			if($image_old != '' && substr_count($image_old,'mw_product') > 0 ) unlink(Mage::getBaseDir('media') . DS.$image_old);
										if($image_old != '' && substr_count($image_old,'mw_product')== 0 ){
					        				if($this ->mwCheckDeleteImage($image_old,$option_id)) @unlink(Mage::getBaseDir('media') . DS.$image_old);
					        			}
				        			}
				        			
				        			
								} catch (Exception $e) {
						      
						        }
						        
							}		 
		        		}
	        		}
	        	}	
        	}
        	// ket thuc them code vao
            $this->setData($option)
                ->setData('product_id', $this->getProduct()->getId())
                ->setData('store_id', $this->getProduct()->getStoreId());

            if ($this->getData('option_id') == '0') {
                $this->unsetData('option_id');
            } else {
                $this->setId($this->getData('option_id'));
            }
            $isEdit = (bool)$this->getId()? true:false;

            if ($this->getData('is_delete') == '1') {
                if ($isEdit) {
                    $this->getValueInstance()->deleteValue($this->getId());
                    $this->deletePrices($this->getId());
                    $this->deleteTitles($this->getId());
                    $this->delete();
                }
            } else {
                if ($this->getData('previous_type') != '') {
                    $previousType = $this->getData('previous_type');
                    //if previous option has dfferent group from one is came now need to remove all data of previous group
                    if ($this->getGroupByType($previousType) != $this->getGroupByType($this->getData('type'))) {

                        switch ($this->getGroupByType($previousType)) {
                            case self::OPTION_GROUP_SELECT:
                                $this->unsetData('values');
                                if ($isEdit) {
                                    $this->getValueInstance()->deleteValue($this->getId());
                                }
                                break;
                            case self::OPTION_GROUP_FILE:
                                $this->setData('file_extension', '');
                                $this->setData('image_size_x', '0');
                                $this->setData('image_size_y', '0');
                                break;
                            case self::OPTION_GROUP_TEXT:
                                $this->setData('max_characters', '0');
                                break;
                            case self::OPTION_GROUP_DATE:
                                break;
                        }
                        if ($this->getGroupByType($this->getData('type')) == self::OPTION_GROUP_SELECT) {
                            $this->setData('sku', '');
                            $this->unsetData('price');
                            $this->unsetData('price_type');
                            if ($isEdit) {
                                $this->deletePrices($this->getId());
                            }
                        }
                    }
                }
                $this->save();            
            }
        }//eof foreach()
        return $this;
    }
}
