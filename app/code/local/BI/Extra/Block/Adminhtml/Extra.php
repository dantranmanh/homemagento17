<?php
class BI_Extra_Block_Adminhtml_Extra extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_extra';
    $this->_blockGroup = 'extra';
    $this->_headerText = Mage::helper('extra')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('extra')->__('Add Item');
    parent::__construct();
  }
}