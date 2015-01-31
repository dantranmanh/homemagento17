<?php
class BI_Extra_Model_Obsever
    {
        public function resetshipping($observer)
        {
            
            $request     = $observer->getEvent()->getRequest();
            $quote    = $observer->getEvent()->getQuote();
			/* Mage::log($request, Zend_Log::DEBUG, 'mwdebug.log');
			Mage::log($quote->getData(), Zend_Log::DEBUG, 'mwdebug.log'); */
            
        }

        
    }