<?php

class MW_Advancedproductoption_Block_Adminhtml_Template_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'advancedproductoption';
        $this->_controller = 'adminhtml_template';
        
        $this->_updateButton('save', 'label', Mage::helper('advancedproductoption')->__('Save Template'));
        $this->_updateButton('delete', 'label', Mage::helper('advancedproductoption')->__('Delete Template'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('advancedproductoption_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'advancedproductoption_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'advancedproductoption_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('advancedproductoption_data') && Mage::registry('advancedproductoption_data')->getId() ) {
            return Mage::helper('advancedproductoption')->__("Edit Template '%s'", $this->htmlEscape(Mage::registry('advancedproductoption_data')->getTitle()));
        } else {
            return Mage::helper('advancedproductoption')->__('Add Template');
        }
    }
}