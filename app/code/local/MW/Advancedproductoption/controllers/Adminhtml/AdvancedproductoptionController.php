<?php

class MW_Advancedproductoption_Adminhtml_AdvancedproductoptionController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('catalog/advancedproductoption')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Template Manager'), Mage::helper('adminhtml')->__('Template Manager'));
		
		return $this;
	}   
 	
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	public function optionsAction()
    {
       $this->loadLayout();
       $this->renderLayout();
       //$this->getResponse()->setBody(
            //$this->getLayout()->createBlock('affiliate/adminhtml_affiliategroup_edit_tab_options', 'admin.group.options')->toHtml()
        //);
    }
	public function productAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	public function productGridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('advancedproductoption/adminhtml_template_edit_tab_product', 'admin.template.products')->toHtml()
        );
    }

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('advancedproductoption/template')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('advancedproductoption_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('catalog/advancedproductoption');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Template Manager'), Mage::helper('adminhtml')->__('Template Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Template News'), Mage::helper('adminhtml')->__('Template News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('advancedproductoption/adminhtml_template_edit'))
				->_addLeft($this->getLayout()->createBlock('advancedproductoption/adminhtml_template_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('advancedproductoption')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			if (isset($data['attribute_set'])){
				$attribute_sets = $data['attribute_set'];
				//zend_debug::dump($stores);die();
				$attributeCount = count($attribute_sets);
				$attributeIndex = 1;
				$attributeData = '';
				foreach ($attribute_sets as $attribute_set){
					$attributeData .= $attribute_set;
					if ($attributeIndex < $attributeCount){
						$attributeData .= ',';
					}
					$attributeIndex++;
				}
				 $data['attribute_set'] = $attributeData;
			}else{
				$data['attribute_set'] = '';
			}
			//zend_debug::dump($data);die();
			$model = Mage::getModel('advancedproductoption/template');		
			$model->setTitle($data['title'])
				  ->setAttributeSet($data['attribute_set'])
				  ->setStatus($data['mw_status'])
				  ->setId($this->getRequest()->getParam('id'));
			try {
				$model->save();
				// them template cho tung product---------------------------------
				$_products = $this->getRequest()->getParam('addproduct');
				$products = $_products['template'];
				//zend_debug::dump($products);die();
				if(isset($products))
				{   
					$collection_products = Mage::getModel('advancedproductoption/templateproduct')->getCollection()
			        									->addFieldToFilter('template_id',$model->getId());
			        if(sizeof($collection_products) > 0){
			        	 foreach ($collection_products as $collection_product) {
			        	 	$collection_product->delete();
			        	 }
			        }
					$this ->saveTemplateProduct($products, $model->getId());	
				}
				//them code cho viec tao template custom option----------------------------------------
				$productData = $this->getRequest()->getPost('product');
				if (isset($productData['options'])) {
					Mage::getModel('advancedproductoption/product_option')->setData($data)->setOptions($productData['options'])->setTemplateId($model->getId())->saveOptions();
		        }
				//ket thuc them code cho viec tao template custom option--------------------------
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('advancedproductoption')->__('Template was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					//zend_debug::dump($this->getRequest()->getParams());die();//affect_product_custom_options,position
					$this->_redirect('*/*/edit', array('_current'=>true,'id' => $model->getId()));
					//zend_debug::dump($this);die();
					//if($this->getRequest()->getParam('affect_product_custom_options') == '1') {
						//$this->_redirect('*/*/edit', array('active_tab'=>'mw_customer_options','id' => $model->getId()));
					//}else if($this->getRequest()->getParam('position')== "") {
						//$this->_redirect('*/*/edit', array('active_tab'=>'mw_form_template_product','id' => $model->getId()));
					//}else{
						//$this->_redirect('*/*/edit', array('active_tab'=>'mw_form_section','id' => $model->getId()));
					//}
						
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('_current'=>true,'id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('advancedproductoption')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
	public function saveTemplateProduct($products, $template_id)
	{
		$product_idss = explode("&",$products);
		$dataproduct = array();
		foreach ($product_idss as $product_ids) {
			$product_id = explode("=",$product_ids);
			$dataproduct['product_id'] = $product_id[0];
			$dataproduct['template_id'] = $template_id;
			if($dataproduct['product_id'] != 0) 
			{
				Mage::getModel("advancedproductoption/templateproduct")->setData($dataproduct)->save();
			}
		}	
		
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$template_id = $this->getRequest()->getParam('id');
				// xoa tat ca du lieu lien quan custom option va template
				$this ->deleteCustomOption($template_id);
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Tempalte was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $templateIds = $this->getRequest()->getParam('template_id');
        if(!is_array($templateIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select template(s)'));
        } else {
            try {
                foreach ($templateIds as $template_id) {
                    
					$this ->deleteCustomOption($template_id);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($templateIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    // ham xoa tat ca cac du lieu co lien quan den custom option va tempalte
    public function deleteCustomOption($template_id)
    {
    	$template = Mage::getModel('advancedproductoption/template')->load($template_id);
        $template->delete();
        // xoa du lieu template product
		$template_products = Mage::getModel('advancedproductoption/templateproduct')->getCollection()
        												->addFieldToFilter('template_id',$template_id);
        if(sizeof($template_products)>0){
             foreach ($template_products as $template_product) {
                    $template_product->delete();
             }
        }
        // xoa du lieu o cac bang custom product
        $collection_options = Mage::getModel('advancedproductoption/product_option')->getCollection()
														->addFieldtoFilter('template_id',$template_id);
														
		if(sizeof($collection_options)>0){
			foreach ($collection_options as $collection_option) {
				$option_id = $collection_option->getOptionId();
				// xoa image kieu text
				$image_old_text = '';
        		$image_old_text = Mage::getModel('advancedproductoption/product_option') ->load($option_id)->getMwImage();
        		if($image_old_text != ''){
        			if($this ->mwCheckDeleteImage($image_old_text)) @unlink(Mage::getBaseDir('media') . DS.$image_old_text);
        		} 
        		
        		// xoa image kieu select
				$collection_image_delete_selects = Mage::getModel('advancedproductoption/product_option_value')->getCollection()
        									   					->addFieldtoFilter('option_id',$option_id);
        									   					
        		if(sizeof($collection_image_delete_selects) > 0){
        			foreach ($collection_image_delete_selects as $collection_image_delete_select) {
        				$image_select_delete = $collection_image_delete_select ->getMwImage();
        				if($image_select_delete != ''){
        					if($this ->mwCheckDeleteImage($image_select_delete)) @unlink(Mage::getBaseDir('media') . DS.$image_select_delete);
        				} 
        			}
        		}
        		
				Mage::getModel('advancedproductoption/product_option')->getValueInstance()->deleteValue($option_id);
                Mage::getModel('advancedproductoption/product_option')->deletePrices($option_id);
                Mage::getModel('advancedproductoption/product_option')->deleteTitles($option_id);
                $collection_option ->delete();
			}
		}
		
    } 
    // ham check xem image co ton tai trong custom option product core khong
    // if co thi khong d phep xoa anh 
    // neu khong thi duoc phep xoa anh 
 	public function mwCheckDeleteImage($image_name)
    {
    	$collection_image_text = Mage::getModel('catalog/product_option')->getCollection()
        										->addFieldtoFilter('mw_image',$image_name);
    	$collection_image_select = Mage::getModel('catalog/product_option_value')->getCollection()
        										  		->addFieldtoFilter('mw_image',$image_name);
        if(sizeof($collection_image_text) == 0 && sizeof($collection_image_select) == 0) return true;
        else return false;
        
    	
    }
	
    public function massStatusAction()
    {
        $templateIds = $this->getRequest()->getParam('template_id');
        if(!is_array($templateIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select template(s)'));
        } else {
            try {
                foreach ($templateIds as $templateId) {
                    $template = Mage::getSingleton('advancedproductoption/template')
                        ->load($templateId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($templateIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'template.csv';
        $content    = $this->getLayout()->createBlock('advancedproductoption/adminhtml_template_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'template.xml';
        $content    = $this->getLayout()->createBlock('advancedproductoption/adminhtml_template_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}