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

/**
 * One page checkout status
 *
 * @category   Mage
 * @category   Mage
 * @package    Mage_Checkout
 @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Webshopapps_Wsafreightcommon_Block_Onepage_ShippingExtra extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {    	
        $this->getCheckout()->setStepData('shippingextra', array(
            'label'     => Mage::helper('checkout')->__('Freight Details'),
            'is_show'   => true
        ));
        
        parent::_construct();
    }
    
    public function getLiftgateRequired()
    {
        if(Mage::getStoreConfig('shipping/wsafreightcommon/default_liftgate',Mage::app()->getStore()) 
        		&& $this->getQuote()->getShippingAddress()->getLiftgateRequired() == ''){
    		return true;
    	} else {
    		return $this->getQuote()->getShippingAddress()->getLiftgateRequired();
    	}
    }
    
    public function getNotifyRequired()
    {
    	return $this->getQuote()->getShippingAddress()->getNotifyRequired();
    }

    public function getInsideDelivery()
    {
        return $this->getQuote()->getShippingAddress()->getInsideDelivery();
    }
    
    public function getShiptoType()
    {
    	return $this->getQuote()->getShippingAddress()->getShiptoType();
    }
    
    public function getShiptoTypeHtmlSelect($defValue=null) {
    
    	if (is_null($defValue)) {
    		$defValue=Mage::getStoreConfig('shipping/wsafreightcommon/default_address_type');
    	}
    
    	$options = Mage::helper('wsafreightcommon')->getOptions();
    
    	$html = $this->getLayout()->createBlock('core/html_select')
    	->setName('shipto_type')
    	->setTitle(Mage::helper('wsafreightcommon')->__('Address Type'))
    	->setId('shipto_type')
    	->setClass('required-entry')
    	->setValue($defValue)
    	->setOptions($options)
    	->getHtml();
    	return $html;
    
    }
}