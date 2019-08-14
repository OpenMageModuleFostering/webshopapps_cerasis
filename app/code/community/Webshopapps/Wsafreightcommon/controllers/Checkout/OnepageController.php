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
require_once 'Mage/Checkout/controllers/OnepageController.php';


class Webshopapps_Wsafreightcommon_Checkout_OnepageController extends Mage_Checkout_OnepageController
{
	private $_skipFreight = false;

    /**
     * save checkout billing address
     */
    public function saveBillingAction()
    {
    	if  (Mage::helper('wsafreightcommon')->getAllFreightCarriers() < 1) {
        		return parent::saveBillingAction();
    	}
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost('billing', array());
            $data = $this->_filterPostData($postData);
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
            if (isset($data['email'])) {
                $data['email'] = trim($data['email']);
            }
            $result = $this->getOnepage()->saveBilling($data, $customerAddressId);
            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getOnepage()->getQuote()->isVirtual()) {
                    $result['goto_section'] = 'payment';
                    $result['update_section'] = array(
                        'name' => 'payment-method',
                        'html' => $this->_getPaymentMethodsHtml()
                    );
                } elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                	if (Mage::helper('wsafreightcommon')->dontShowCommonFreight(
						$this->getOnepage()->getQuote()->getAllVisibleItems())) {
							$this->_skipFreight=true;
					}
                    if(!Mage::helper('wsafreightcommon')->displayAccessorialsAtCheckout()) {
                        $this->_skipFreight=true;
                    }

                	if ($this->_skipFreight) {
                		$result['goto_section'] = 'shipping_method';

	                    $result['update_section'] = array(
	                        'name' => 'shipping-method',
	                        'html' => $this->_getShippingMethodsHtml()
	                    );
                	} else {

	                       $result['goto_section'] = 'shippingextra';
                	}

                    $result['allow_sections'] = array('shipping');
                    $result['duplicateBillingInfo'] = 'true';
                } else {
                    $result['goto_section'] = 'shipping';
                }
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    public function saveShippingAction()
    {
    	if (!Mage::getStoreConfig('shipping/wsafreightcommon/active')) {
    		return parent::saveShippingAction();
    	}
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getOnepage()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
            	if (Mage::helper('wsafreightcommon')->dontShowCommonFreight(
						$this->getOnepage()->getQuote()->getAllVisibleItems()) || !Mage::helper('wsafreightcommon')->displayAccessorialsAtCheckout()) {
					$this->_skipFreight=true;
            		$result['goto_section'] = 'shipping_method';
	                $result['update_section'] = array(
	                    'name' => 'shipping-method',
	                    'html' => $this->_getShippingMethodsHtml()
	                );
            	} else {
	                $result['goto_section'] = 'shippingextra';
	               /* $result['update_section'] = array(
	                    'name' => 'shippingextra',
	                    'html' => $this->_getShippingExtraHtml()
	                );*/
            	}


            }
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    protected function _getShippingExtraHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('wsafreightcommon_onepage_shippingextra');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

    public function saveShippingExtraAction()
    {
    	$this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $liftgateReqd = $this->getRequest()->getPost('liftgate_required', '');
            $notifyReqd = $this->getRequest()->getPost('allow_notify', '');
            $insideDelivery = $this->getRequest()->getPost('inside_delivery', '');
            
            $addressType = $this->getRequest()->getPost('shipto_type', '');
            $result = $this->getOnepage()->saveShippingExtra($liftgateReqd,$addressType,$notifyReqd,$insideDelivery);
            /*
            $result will have erro data if shipping method is empty
            */
            if(!$result) {

                $result['goto_section'] = 'shipping_method';
                $result['update_section'] = array(
                    'name' => 'shipping-method',
                    'html' => $this->_getShippingMethodsHtml()
                );

            }
            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
}
