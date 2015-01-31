<?php
class BI_Extra_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/extra?id=15 
    	 *  or
    	 * http://site.com/extra/id/15 	
    	 */
    	/* 
		$extra_id = $this->getRequest()->getParam('id');

  		if($extra_id != null && $extra_id != '')	{
			$extra = Mage::getModel('extra/extra')->load($extra_id)->getData();
		} else {
			$extra = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($extra == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$extraTable = $resource->getTableName('extra');
			
			$select = $read->select()
			   ->from($extraTable,array('extra_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$extra = $read->fetchRow($select);
		}
		Mage::register('extra', $extra);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}