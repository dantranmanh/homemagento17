<?php
class MW_Advancedproductoption_Block_Adminhtml_Template extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_template';
    $this->_blockGroup = 'advancedproductoption';
    $this->_headerText = Mage::helper('advancedproductoption')->__('List of templates');
    $this->_addButtonLabel = Mage::helper('advancedproductoption')->__('Add Template');
    parent::__construct();
  }
}