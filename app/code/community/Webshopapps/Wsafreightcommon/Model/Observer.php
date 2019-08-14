<?php

/**
 * Magento Webshopapps Module
 *
 * @category   Webshopapps
 * @package    Webshopapps Wsafreightcommon
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    www.webshopapps.com/license/license.txt
 * @author     Karen Baker <sales@webshopapps.com>
 */

class Webshopapps_Wsafreightcommon_Model_Observer extends Mage_Core_Model_Abstract
{
	public function postError($observer) {

		$allFreightCarriers = Mage::helper('wsafreightcommon')->getAllFreightCarriers();

		if (in_array('yrcfreight', $allFreightCarriers)){

			if (!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMveXJjZnJlaWdodC9zaGlwX29uY2U=','eXVtbXlnbGFzcw==','Y2FycmllcnMveXJjZnJlaWdodC9zZXJpYWw=')){
				$session = Mage::getSingleton('adminhtml/session');
				$session->addError(Mage::helper('adminhtml')->__(base64_decode('U2VyaWFsIEtleSBJcyBOT1QgVmFsaWQgZm9yIFdlYlNob3BBcHBzIFlSQyBGcmVpZ2h0')))  ;
			}
		}
		if (in_array('wsaupsfreight', $allFreightCarriers)){

			if (!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvd3NhdXBzZnJlaWdodC9zaGlwX29uY2U=','b25zaWRl','Y2FycmllcnMvd3NhdXBzZnJlaWdodC9zZXJpYWw=')){
				$session = Mage::getSingleton('adminhtml/session');
				$session->addError(Mage::helper('adminhtml')->__(base64_decode('U2VyaWFsIEtleSBJcyBOT1QgVmFsaWQgZm9yIFdlYlNob3BBcHBzIFVQUyBGcmVpZ2h0')))  ;
			}
		}
		if (in_array('wsafedexfreight', $allFreightCarriers)){

			if (!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvd3NhZmVkZXhmcmVpZ2h0L3NoaXBfb25jZQ==','d2Fyd29ybGQ=','Y2FycmllcnMvd3NhZmVkZXhmcmVpZ2h0L3NlcmlhbA==')){
				$session = Mage::getSingleton('adminhtml/session');
				$session->addError(Mage::helper('adminhtml')->__(base64_decode('U2VyaWFsIEtleSBJcyBOT1QgVmFsaWQgZm9yIFdlYlNob3BBcHBzIEZlZGV4IEZyZWlnaHQ=')));
			}
		}
		if (in_array('rlfreight', $allFreightCarriers)){

			if(!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvcmxmcmVpZ2h0L3NoaXBfb25jZQ==','d2luZG93bW9vbg==','Y2FycmllcnMvcmxmcmVpZ2h0L3NlcmlhbA==')){
				$session = Mage::getSingleton('adminhtml/session');
				$session->addError(Mage::helper('adminhtml')->__(base64_decode('U2VyaWFsIEtleSBJcyBOT1QgVmFsaWQgZm9yIFdlYlNob3BBcHBzIFJMIEZyZWlnaHQ=')));
			}
		}
		if (in_array('echofreight', $allFreightCarriers)){

			if(!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvZWNob2ZyZWlnaHQvc2hpcF9vbmNl','d2VuZHlob3VzZQ==','Y2FycmllcnMvZWNob2ZyZWlnaHQvc2VyaWFs')) {
				$session = Mage::getSingleton('adminhtml/session');
				$session->addError(Mage::helper('adminhtml')->__(base64_decode('CVNlcmlhbCBLZXkgSXMgTk9UIFZhbGlkIGZvciBXZWJTaG9wQXBwcyBFY2hvIEZyZWlnaHQ=')));
			}
		}
		if (in_array('abffreight', $allFreightCarriers)){

			if(!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvYWJmZnJlaWdodC9zaGlwX29uY2U=','YWJmdXBzaWRl','Y2FycmllcnMvYWJmZnJlaWdodC9zZXJpYWw=')) {
				$session = Mage::getSingleton('adminhtml/session');
				$session->addError(Mage::helper('adminhtml')->__(base64_decode('CVNlcmlhbCBLZXkgSXMgTk9UIFZhbGlkIGZvciBXZWJTaG9wQXBwcyBBQkYgRnJlaWdodA==')))  ;
			}
		}
        if (in_array('newgistics', $allFreightCarriers)){

			if (!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvbmV3Z2lzdGljcy9zaGlwX29uY2U=','aHVsa3NtYXNo','Y2FycmllcnMvbmV3Z2lzdGljcy9zZXJpYWw=')) {
				$session = Mage::getSingleton('adminhtml/session');
				$session->addError(Mage::helper('adminhtml')->__(base64_decode('U2VyaWFsIEtleSBJcyBOT1QgVmFsaWQgZm9yIFdlYlNob3BBcHBzIE5ld2dpc3RpY3MgRnJlaWdodA==')));
			}
		}
		if (in_array('conwayfreight', $allFreightCarriers)){

			if (!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvY29ud2F5ZnJlaWdodC9zaGlwX29uY2U=','aGVyZWlhbQ==','Y2FycmllcnMvY29ud2F5ZnJlaWdodC9zZXJpYWw=')) {
				$session = Mage::getSingleton('adminhtml/session');
				$session->addError(Mage::helper('adminhtml')->__(base64_decode('U2VyaWFsIEtleSBJcyBOT1QgVmFsaWQgZm9yIFdlYlNob3BBcHBzIENvbi1XYXkgRnJlaWdodA==')));
			}
		}
		if (in_array('estesfreight', $allFreightCarriers)){

			if (!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvZXN0ZXNmcmVpZ2h0L3NoaXBfb25jZQ==','b3ZlcmxhbmRlcg==','Y2FycmllcnMvZXN0ZXNmcmVpZ2h0L3NlcmlhbA==')) {
				$session = Mage::getSingleton('adminhtml/session');
				$session->addError(Mage::helper('adminhtml')->__(base64_decode('U2VyaWFsIEtleSBJcyBOT1QgVmFsaWQgZm9yIFdlYlNob3BBcHBzIEVzdGVzIEZyZWlnaHQ=')));
			}
		}
		if (in_array('cerasisfreight', $allFreightCarriers)){

			if (!Mage::helper('wsacommon')->checkItems('Y2FycmllcnMvY2VyYXNpc2ZyZWlnaHQvc2hpcF9vbmNl','Z3JpenpseWJlYXI=','Y2FycmllcnMvY2VyYXNpc2ZyZWlnaHQvc2VyaWFs')){
				$session = Mage::getSingleton('adminhtml/session');
				$session->addError(Mage::helper('adminhtml')->__(base64_decode('U2VyaWFsIEtleSBJcyBOT1QgVmFsaWQgZm9yIFdlYlNob3BBcHBzIENlcmFzaXMgRnJlaWdodA==')));
			}
		}
	}

   public function hookToControllerActionPreDispatch($observer) {
       $actionName = $observer->getEvent()->getControllerAction()->getFullActionName();

       	//we compare action name to see if that's action for which we want to add our own event
        if($actionName == 'checkout_cart_estimatePost')
        {
        	$request = $observer->getControllerAction()->getRequest();
        	$country    		= (string) $request->getParam('country_id');
	        $postcode   		= (string) $request->getParam('estimate_postcode');
	        $city       		= (string) $request->getParam('estimate_city');
	        $regionId   		= (string) $request->getParam('region_id');
	        $region     		= (string) $request->getParam('region');
	        $liftgateRequired	= (string) $request->getParam('liftgate_required');
            $notifyRequired     = (string) $request->getParam('notify_required');
            $insideDelivery     = (string) $request->getParam('inside_delivery');
            $shiptoType			= (string) $request->getParam('shipto_type');

            switch ($shiptoType) {
                case 0:
                    $destType = 'RES';
                    break;
                case 1:
                    $destType = 'COM';
                    break;
                default:
                    $destType = 'RES';
                    break;
            }

	        $this->_getQuote()->getShippingAddress()
	            ->setCountryId($country)
	            ->setCity($city)
	            ->setPostcode($postcode)
	            ->setRegionId($regionId)
	            ->setRegion($region)
	            ->setLiftgateRequired($liftgateRequired)
	            ->setShiptoType($shiptoType)
				->setDestType($destType)
                ->setNotifyRequired($notifyRequired)
                ->setInsideDelivery($insideDelivery)
                ->setCollectShippingRates(true);
	        $this->_getQuote()->save();
        }
    }

    public function hookToControllerActionPostDispatch($observer) {

      $orderStore = $this->_getQuote()->getStore();
      $showCheapest = Mage::getStoreConfig('shipping/wsafreightcommon/auto_select_cheapest',  $orderStore);


       if($showCheapest){

        $actionName = $observer->getEvent()->getControllerAction()->getFullActionName();

        if($actionName == 'checkout_cart_estimatePost' || $actionName == 'checkout_cart_add' || $actionName == 'checkout_cart_updatePost' || 'checkout_onepage_saveShippingExtra')
        {
            $method = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getShippingMethod();

            if(!empty($method)) { return; }

            $rates = Mage::getSingleton('checkout/session')->getQuote()
                ->getShippingAddress()->getGroupedAllShippingRates();

            if(!count($rates)) {
                Mage::getSingleton('checkout/session')->getQuote()
                    ->getShippingAddress()->collectShippingRates();

                $rates = Mage::getSingleton('checkout/session')->getQuote()
                    ->getShippingAddress()->getGroupedAllShippingRates();
            }

            if (count($rates)) {
                $topRate = null;
                foreach($rates as $rateArray) {
                    $cheapest = reset($rateArray);

                    if($topRate) {
                        if($cheapest->getPrice() < $topRate->getPrice()) {
                            $topRate = $cheapest;
                        }
                    } else {
                        $topRate = $cheapest;
                    }
                }

                $code = $topRate->code;

                try {
                    Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()
                    ->setShippingMethod($code);

                    Mage::getSingleton('checkout/session')->getQuote()->save();

                    Mage::getSingleton('checkout/session')->resetCheckout();

                    unset($cheapest);
                    unset($topRate);

                }
                catch (Mage_Core_Exception $e) {
                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
                }
                catch (Exception $e) {
                    Mage::getSingleton('checkout/session')->addException(
                            $e, Mage::helper('checkout')->__('Load customer quote error')
                    );
                }
            }
        }
      }
    }

	protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

   	protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }
}