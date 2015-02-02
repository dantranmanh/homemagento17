<?php

class MW_Advancedproductoption_Block_Adminhtml_Catalog_Product_Edit_Tab_Template extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('option_template_form', array('legend'=>Mage::helper('advancedproductoption')->__('Option Template')));
     
      $fieldset->addField('mw_template_id', 'select', array(
          'label'     => Mage::helper('advancedproductoption')->__('Template Model'),
          'name'      => 'mw_template_id',
          'values'    => $this->_getGroupArray(),
          'note' => Mage::helper('advancedproductoption')->__('Please Select Template Options and Save the product to apply options'),
          //'value'     => $template_id
      ));
      $fieldset->addField('mw_sku', 'text', array(
          'label'     => Mage::helper('advancedproductoption')->__('Copy product options by SKU'),
          'name'      => 'mw_sku',
          'note' => Mage::helper('advancedproductoption')->__('Please insert product SKU and Save the product to apply options'),
          //'value'     => $template_id
      ));
     
     
      return parent::_prepareForm();
  }
  	public function checkAttributeSet($attribute_set_id, $string_attribute_sets) 
  	{
  		if($string_attribute_sets == '') return false;
  		
  		$array_attribute_sets = explode(',',$string_attribute_sets);
  		if(in_array($attribute_set_id, $array_attribute_sets)) return true;
  		else return false;
  		
  	}
  	public function checkProductTemplate($product_id,$template_id)
  	{
  		$template_products = Mage::getModel('advancedproductoption/templateproduct')->getCollection()
													   ->addFieldToFilter('product_id',$product_id)
			        								   ->addFieldToFilter('template_id',$template_id);
		if(sizeof($template_products) > 0) return true;
		else return false;
  		
  	}
  	
	private function _getGroupArray()
    {
    	$arr = array();
    	$arr[''] = Mage::helper('advancedproductoption')->__('Please select a template');
    	$product_id = $this->getRequest()->getParam('id');
    	if($product_id){
	    	$attribute_set_id = Mage::getModel('catalog/product')->load($product_id)->getAttributeSetId();
	    	
	    	$collection_templates = Mage::getModel('advancedproductoption/template')->getCollection()
				   ->addFieldToFilter('status',MW_Advancedproductoption_Model_Status::STATUS_ENABLED);
		
			foreach ($collection_templates as $collection_template) 
			{  
				$template_id = $collection_template ->getTemplateId();
				$title = $collection_template ->getTitle();
				$string_attribute_sets = $collection_template ->getAttributeSet();
				$check_product = $this ->checkProductTemplate($product_id,$template_id);
				if($check_product) $arr[$template_id] =  $title;
				else{
					$check_attribute = $this ->checkAttributeSet($attribute_set_id, $string_attribute_sets); 
					if($check_attribute) $arr[$template_id] =  $title;
				}
			}
    		
    	}else{
    		$attribute_set_id = $this->getRequest()->getParam('set');
    		if($attribute_set_id){
    			$collection_templates = Mage::getModel('advancedproductoption/template')->getCollection()
				  	 ->addFieldToFilter('status',MW_Advancedproductoption_Model_Status::STATUS_ENABLED);
		
				foreach ($collection_templates as $collection_template) 
				{ 
					$template_id = $collection_template ->getTemplateId();
					$title = $collection_template ->getTitle();
					$string_attribute_sets = $collection_template ->getAttributeSet();
					$check_attribute = $this ->checkAttributeSet($attribute_set_id, $string_attribute_sets); 
					if($check_attribute) $arr[$template_id] =  $title;
					
				}
    			
    		}
    		
    	}
    		   
    	
		return $arr;
    }
}