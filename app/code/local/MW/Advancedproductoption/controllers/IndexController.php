<?php
class MW_Advancedproductoption_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	/*
    	$resource = Mage::getSingleton('core/resource');
    	$writeConnection = $resource->getConnection('core_write');
    	$table = $resource->getTableName('catalog/product_option_type_value');
    	$option_type_id = 25;
    	$mw_qty = 6;
		$query = "UPDATE {$table} SET mw_qty = '{$mw_qty}' WHERE option_type_id = ". $option_type_id;
        $writeConnection->query($query);
    	echo 'ngon';
	    */            
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/advancedproductoption?id=15 
    	 *  or
    	 * http://site.com/advancedproductoption/id/15 	
    	 */
    	/* 
		$advancedproductoption_id = $this->getRequest()->getParam('id');

  		if($advancedproductoption_id != null && $advancedproductoption_id != '')	{
			$advancedproductoption = Mage::getModel('advancedproductoption/advancedproductoption')->load($advancedproductoption_id)->getData();
		} else {
			$advancedproductoption = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($advancedproductoption == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$advancedproductoptionTable = $resource->getTableName('advancedproductoption');
			
			$select = $read->select()
			   ->from($advancedproductoptionTable,array('advancedproductoption_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$advancedproductoption = $read->fetchRow($select);
		}
		Mage::register('advancedproductoption', $advancedproductoption);
		*/

			
		//$this->loadLayout();     
		//$this->renderLayout();
    }
}