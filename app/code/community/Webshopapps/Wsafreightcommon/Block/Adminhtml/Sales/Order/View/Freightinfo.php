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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * @category   Webshopapps
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @author     Karen Baker
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */


class Webshopapps_Wsafreightcommon_Block_Adminhtml_Sales_Order_View_Freightinfo
extends Mage_Adminhtml_Block_Sales_Order_View_Info
{

	public function getWsafreightcommonInfoHtml() {

		$order = $this->getOrder();
		$htmlOutput = '';
		$innerHtmlOutput = $this->getFreightShippingInfo($order);
		
		if (!empty($innerHtmlOutput)) {

			$htmlOutput = '<div class="box-right"><div class="clear"></div><div class="entry-edit">';
			$htmlOutput.= '<div class="entry-edit-head">';
			$htmlOutput.= '<h4 class="icon-head head-shipping-method">';
			$htmlOutput.= Mage::helper("sales")->__("Freight Shipping Information");
			$htmlOutput.= '</h4>';
			$htmlOutput.= '</div><fieldset>';
			$htmlOutput.= $innerHtmlOutput;
			$htmlOutput.= '</fieldset> <div class="clear"/></div></div>';
		}
		return "'".$htmlOutput."'";
	}
	
	public function getFreightShippingInfo($order) {
        $innerHtmlOutput = '';
		if ($order->getFreightQuoteId()) {
            $innerHtmlOutput.=  Mage::helper('sales')->__('Freight Reference Id - %s', $order->getFreightQuoteId()) ;
            $innerHtmlOutput .= '<br />';
        }
        if ($order->getLiftgateRequired()) {
            $innerHtmlOutput.= Mage::helper('sales')->__('Liftgate Required');
            $innerHtmlOutput .= '<br />';
        }
        if ($order->getNotifyRequired()) {
            $innerHtmlOutput.= Mage::helper('sales')->__('Scheduled Appointment Required') ;
            $innerHtmlOutput .= '<br />';
        }
        if ($order->getInsideDelivery()) {
            $innerHtmlOutput.= Mage::helper('sales')->__('Inside Delivery Required') ;
            $innerHtmlOutput .= '<br />';
        }
        if (($order->getShiptoType()!='') && !Mage::getStoreConfig('shipping/wsafreightcommon/default_address',Mage::app()->getStore())) {
            if ($order->getShiptoType() == 0) {
                $innerHtmlOutput .= Mage::helper('sales')->__('Address Type - Residential');
            } else {
                $innerHtmlOutput .= Mage::helper('sales')->__('Address Type - Business');
            }
            $innerHtmlOutput .= '<br />';
        } else if (($order->getShiptoType()!='') && Mage::getStoreConfig('shipping/wsafreightcommon/default_address',Mage::app()->getStore()))  {
            if ($order->getShiptoType() == 0)  {
                $innerHtmlOutput .= Mage::helper('sales')->__('Address Type - Business');
            } else {
                $innerHtmlOutput .= Mage::helper('sales')->__('Address Type - Residential');
            }
            $innerHtmlOutput .= '<br />';
        }
        
        return $innerHtmlOutput;
		
	}

}
