<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Webshopapps_Wsafreightcommon_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
	
	public function saveShippingExtra($liftgateRqd,$addressType,$notifyRqd="",$insideDelivery="")
    {
    	$addresses=$this->getQuote()->getAllShippingAddresses();
    	
    	foreach ($addresses as $address) {
    		
    		if ($liftgateRqd != '') {
	            $address->setLiftgateRequired($liftgateRqd);
	        } else {
	            $address->setLiftgateRequired(0);
	        }
	        
	        if ($notifyRqd != '') {
	        	$address->setNotifyRequired($notifyRqd);
	        } else {
	        	$address->setNotifyRequired(0);
	        }
	        
    	   if ($insideDelivery != '') {
                $address->setInsideDelivery($insideDelivery);
            } else {
                $address->setInsideDelivery(0);
            }
	
	        if ($addressType != '') {
	            $address->setShiptoType($addressType);
	        } else {
	            $address->setShiptoType("");
	        }
	        $address->setCollectShippingRates(true);
	        
    	}
		
        $this->getQuote()->collectTotals();
        $this->getQuote()->save();
	  
        $this->getCheckout()
            ->setStepData('shippingextra', 'complete', true)
            ->setStepData('shippingextra', 'allow', true)
            ->setStepData('shipping-method', 'allow', true);

        return array();
    }
    
      /**
     * Enter description here...
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function initCheckout()
    {
        $checkout = $this->getCheckout();
        $customerSession = $this->getCustomerSession();
        if (is_array($checkout->getStepData())) {
            foreach ($checkout->getStepData() as $step=>$data) {
                if (!($step==='login' || $customerSession->isLoggedIn() && $step==='billing')) {
                    $checkout->setStepData($step, 'allow', false);
                }
            }
        }
        
        $checkout->setStepData('shippingextra', 'allow', true);
        
        /**
         * Reset multishipping flag before any manipulations with quote address
         * addAddress method for quote object related on this flag
         */
        if ($this->getQuote()->getIsMultiShipping()) {
            $this->getQuote()->setIsMultiShipping(false);
            $this->getQuote()->save();
        }

        /*
        * want to laod the correct customer information by assiging to address
        * instead of just loading from sales/quote_address
        */
        $customer = $customerSession->getCustomer();
        if ($customer) {
            $this->getQuote()->assignCustomer($customer);
        }
        return $this;
    }
    
    
}