<?php

class BI_Extra_Block_Adminhtml_Extra_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'extra';
        $this->_controller = 'adminhtml_extra';
        
        $this->_updateButton('save', 'label', Mage::helper('extra')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('extra')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('extra_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'extra_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'extra_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('extra_data') && Mage::registry('extra_data')->getId() ) {
            return Mage::helper('extra')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('extra_data')->getTitle()));
        } else {
            return Mage::helper('extra')->__('Add Item');
        }
    }
}