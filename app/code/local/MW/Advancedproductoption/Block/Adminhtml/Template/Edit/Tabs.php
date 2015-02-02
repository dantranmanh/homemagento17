<?php

class MW_Advancedproductoption_Block_Adminhtml_Template_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('template_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('advancedproductoption')->__('Template Information'));
  }

  protected function _beforeToHtml()
  {	
      $this->addTab('mw_form_section', array(
          'label'     => Mage::helper('advancedproductoption')->__('General Information'),
          'title'     => Mage::helper('advancedproductoption')->__('General Information'),
          'content'   => $this->getLayout()->createBlock('advancedproductoption/adminhtml_template_edit_tab_form')->toHtml(),
      ));
      $this->addTab('mw_customer_options', array(
             'label'     => Mage::helper('advancedproductoption')->__('Option Template'),
             'url'       => $this->getUrl('*/*/options', array('_current' => true)),
             'class'     => 'ajax',
      		 'active'    =>true,
      ));
      $this->addTab('mw_form_template_product', array(
             'label'     => Mage::helper('advancedproductoption')->__('Products'),
             'url'       => $this->getUrl('*/*/product', array('_current' => true)),
             'class'     => 'ajax',
      		 //'active'    => true,
            ));
      return parent::_beforeToHtml();
  }
}