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
 * Onepage checkout block
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Webshopapps_Wsafreightcommon_Block_Onepage extends Mage_Checkout_Block_Onepage
{
    public function getSteps()
    {
    	if  (Mage::helper('wsafreightcommon')->getAllFreightCarriers() < 1) {
    		return parent::getSteps();
    	}
    	
        $steps = array();

        if (!$this->isCustomerLoggedIn()) {
            $steps['login'] = $this->getCheckout()->getStepData('login');
        }

    	if (Mage::helper('wsafreightcommon')->dontShowCommonFreight(
			$this->getQuote()->getAllVisibleItems())) {
				$skipExtras=true;
		} else {
			$skipExtras=false;
		}

        $stepCodes = array('billing', 'shipping', 'shippingextra', 'shipping_method', 'payment', 'review');

        foreach ($stepCodes as $step) {
        	if ($skipExtras && $step=='shippingextra') {
        		continue;
        	}
            $steps[$step] = $this->getCheckout()->getStepData($step);
        }
        return $steps;
    }


}