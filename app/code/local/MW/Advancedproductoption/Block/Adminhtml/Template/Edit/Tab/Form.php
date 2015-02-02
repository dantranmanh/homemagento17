<?php

class MW_Advancedproductoption_Block_Adminhtml_Template_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('template_form', array('legend'=>Mage::helper('advancedproductoption')->__('Template information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('advancedproductoption')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

	   $entityType = Mage::getModel('eav/config')->getEntityType('catalog_product');
	   $values_attribute_set = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter($entityType->getId())
                ->load()
                ->toOptionArray();
                
       $fieldset->addField('attribute_set', 'multiselect', array(
            'label' => Mage::helper('advancedproductoption')->__('Attribute Sets'),
            'title' => Mage::helper('advancedproductoption')->__('Attribute Sets'),
            'name'  => 'attribute_set[]',
            //'value' => $entityType->getDefaultAttributeSetId(),
            'values'=> $values_attribute_set
        ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('advancedproductoption')->__('Status'),
          'name'      => 'mw_status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('advancedproductoption')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('advancedproductoption')->__('Disabled'),
              ),
          ),
      ));
      
     
      if ( Mage::getSingleton('adminhtml/session')->getAdvancedproductoptionData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getAdvancedproductoptionData());
          Mage::getSingleton('adminhtml/session')->setAdvancedproductoptionData(null);
      } elseif ( Mage::registry('advancedproductoption_data') ) {
          $form->setValues(Mage::registry('advancedproductoption_data')->getData());
      }
      return parent::_prepareForm();
  }
}