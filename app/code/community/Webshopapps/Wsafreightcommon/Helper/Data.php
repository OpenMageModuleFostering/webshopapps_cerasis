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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog data helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Webshopapps_Wsafreightcommon_Helper_Data extends Mage_Core_Helper_Abstract
{

    protected static $_fixLiftgateFee;
    protected static $_fixDeliveryType;
    protected static $_residentialFee;
    protected static $_liftgateFee;
    protected static $_insideDeliveryFee;
    protected static $_liveAccessories;
    protected static $_hazardous;
    protected static $_defaultFreightClass;
    protected static $_minFreightWeight;
    protected static $_fixedNotifyRequired;
    protected static $_active;
    protected static $_freeFreightResidentialFee;
    protected static $_freeFreightLiftgateFee;

    protected static $_possibleFreightCarriers = array(
        'Webshopapps_Wsafreightcommon' => 'freefreight',
        'Webshopapps_Cerasisfreight' => 'cerasisfreight',
        'Webshopapps_Ctsfreight' => 'ctsfreight',
        'Webshopapps_Dmtrans' => 'dmtrans',
        'Webshopapps_Newgistics' => 'newgistics',
        'Webshopapps_Abffreight' => 'abffreight',
        'Webshopapps_Wsafedexfreight' => 'wsafedexfreight',
        'Webshopapps_Conwayfreight' => 'conwayfreight',
        'Webshopapps_Estesfreight' => 'estesfreight',
        'Webshopapps_Echofreight' => 'echofreight',
        'Webshopapps_Rlfreight' => 'rlfreight',
        'Webshopapps_Wsaupsfreight' => 'wsaupsfreight',
        'Webshopapps_Yrcfreight' => 'yrcfreight',
        'Webshopapps_Wsaolddominion' => 'wsaolddominion',
    );

    public static function getDefaultFreightClass()
    {
        if (self::$_defaultFreightClass == NULL) {
            self::$_defaultFreightClass = Mage::getStoreConfig('shipping/wsafreightcommon/default_freight_class');
        }
        return self::$_defaultFreightClass;
    }

    public static function isActive()
    {
        if (self::$_active == NULL) {
            self::$_active = count(self::getAllFreightCarriers()) > 0;
        }
        return self::$_active;
    }

    public static function isFixedLiftgateFee()
    {
        if (self::$_fixLiftgateFee == NULL) {
            self::$_fixLiftgateFee = Mage::getStoreConfig('shipping/wsafreightcommon/apply_live_liftgate');
        }
        return self::$_fixLiftgateFee;
    }

    public static function isFixedDeliveryType()
    {
        if (self::$_fixDeliveryType == NULL) {
            self::$_fixDeliveryType = Mage::getStoreConfig('shipping/wsafreightcommon/apply_live_business');
        }
        return self::$_fixDeliveryType;
    }

    public static function isNotifyRequired()
    {
        if (self::$_fixedNotifyRequired == NULL) {
            self::$_fixedNotifyRequired = Mage::getStoreConfig('shipping/wsafreightcommon/apply_live_notify');
        }
        return self::$_fixedNotifyRequired;
    }

    public static function getResidentialFee()
    {
        if (self::$_residentialFee == NULL) {
            self::$_residentialFee = Mage::getStoreConfig('shipping/wsafreightcommon/residential_fee');
        }
        return self::$_residentialFee;
    }

    public static function getLiftgateFee()
    {
        if (self::$_liftgateFee == NULL) {
            self::$_liftgateFee = Mage::getStoreConfig('shipping/wsafreightcommon/liftgate_fee');
        }
        return self::$_liftgateFee;
    }

    public static function getInsideDeliveryFee()
    {
        if (self::$_insideDeliveryFee == NULL) {
            self::$_insideDeliveryFee = Mage::getStoreConfig('shipping/wsafreightcommon/inside_delivery_fee');
        }
        return self::$_insideDeliveryFee;
    }

    public static function getNotifyFee()
    {
        if (self::$_insideDeliveryFee == NULL) {
            self::$_insideDeliveryFee = Mage::getStoreConfig('shipping/wsafreightcommon/notify_fee');
        }
        return self::$_insideDeliveryFee;
    }

    public static function getUseLiveAccessories()
    {
        if (self::$_liveAccessories == NULL) {
            self::$_liveAccessories = Mage::getStoreConfig('shipping/wsafreightcommon/use_accessories');
        }
        return self::$_liveAccessories;
    }

    public static function isHazardous()
    {
        if (self::$_hazardous == NULL) {
            self::$_hazardous = Mage::getStoreConfig('shipping/wsafreightcommon/hazardous');
        }
        return self::$_hazardous;
    }

    public static function getMinFreightWeight()
    {
        if (self::$_minFreightWeight == NULL) {
            self::$_minFreightWeight = Mage::getStoreConfig('shipping/wsafreightcommon/min_weight');
        }
        return self::$_minFreightWeight;
    }

    public static function getFreeFreightResidentialFee()
    {
        if (self::$_freeFreightResidentialFee == NULL) {
            self::$_freeFreightResidentialFee = Mage::getStoreConfig('carriers/freefreight/residential_fee');
        }
        return self::$_freeFreightResidentialFee;
    }

    public static function getFreeFreightLiftgateFee()
    {
        if (self::$_freeFreightLiftgateFee == NULL) {
            self::$_freeFreightLiftgateFee = Mage::getStoreConfig('carriers/freefreight/liftgate_fee');
        }
        return self::$_freeFreightLiftgateFee;
    }

    public function isResSelectorEnabled()
    {
        if (Mage::helper('wsacommon')->isModuleEnabled('Webshopapps_Desttype', 'shipping/desttype/active')) {
            return true;
        }
        return false;
    }

    /**
     * Retrieves enabled freight carriers.
     */
    public static function getAllFreightCarriers()
    {
        $enabledCarriers = array();

        foreach (self::$_possibleFreightCarriers as $freightModuleName => $freightShortName) {
            if (Mage::helper('wsacommon')->isModuleEnabled($freightModuleName,
                'carriers/' . $freightShortName . '/active')
            ) {
                $enabledCarriers[] = $freightShortName;
            }

        }

        return $enabledCarriers;
    }

    public function showOnlyCommonFreight($items, $cartWeight)
    {
        $restrictRates = Mage::getStoreConfig('shipping/wsafreightcommon/restrict_rates');
        $forceFreight = Mage::getStoreConfig('shipping/wsafreightcommon/force_freight');
        $hasFreightItems = $this->hasFreightItems($items);

        if (($restrictRates && $cartWeight >= Mage::getStoreConfig('shipping/wsafreightcommon/min_weight')) ||
            ($forceFreight && $hasFreightItems)
        ) {
            return true;
        }
        return false;
    }

    public function dontShowCommonFreight($items, $cartWeight = null)
    {

        if (is_null($cartWeight)) {
            $cartWeight = $this->getWeight($items);
        }
        $hasFreightItems = $this->hasFreightItems($items);
        if ($cartWeight < Mage::getStoreConfig('shipping/wsafreightcommon/min_weight') &&
            !$hasFreightItems
        ) {
            return true;
        }
        return false;
    }

    public function displayAccessorialsAtCheckout()
    {
        $display = true;
        if (Mage::getStoreConfig('shipping/wsafreightcommon/apply_live_liftgate') && Mage::getStoreConfig('shipping/wsafreightcommon/apply_live_business') && (!$this->isNotifyOptionEnabled() || ($this->isNotifyOptionEnabled() && Mage::getStoreConfig('shipping/wsafreightcommon/apply_live_notify')))) {
            $display = false;
        }
        return $display;
    }

    private function getWeight($items)
    {
        $addressWeight = 0;
        foreach ($items as $item) {
            /**
             * Skip if this item is virtual
             */

            if ($item->getProduct()->isVirtual()) {
                continue;
            }
            /**
             * Children weight we calculate for parent
             */
            if ($item->getParentItem()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isShipSeparately()) {
                foreach ($item->getChildren() as $child) {
                    if ($child->getProduct()->isVirtual()) {
                        continue;
                    }

                    if (!$item->getProduct()->getWeightType()) {
                        $itemWeight = $child->getWeight();
                        $itemQty = $item->getQty() * $child->getQty();
                        $rowWeight = $itemWeight * $itemQty;
                        $addressWeight += $rowWeight;

                    }
                }
                if ($item->getProduct()->getWeightType()) {
                    $itemWeight = $item->getWeight();
                    $rowWeight = $itemWeight * $item->getQty();
                    $addressWeight += $rowWeight;

                }
            } else {

                $itemWeight = $item->getWeight();
                $rowWeight = $itemWeight * $item->getQty();
                $addressWeight += $rowWeight;

            }
        }
        return $addressWeight;
    }

    public function getOptions()
    {
        $enabledFreightCarriers = $this->getAllFreightCarriers();
        if (in_array("echofreight", $enabledFreightCarriers)) {
            if (Mage::getStoreConfig('shipping/wsafreightcommon/default_address', Mage::app()->getStore())) {
                $options = array(
                    $this->__('Business'),
                    $this->__('Residential'),
                    $this->__('Construction Site'),
                    $this->__('Trade Show')
                );
            } else {
                $options = array(
                    $this->__('Residential'),
                    $this->__('Business'),
                    $this->__('Construction Site'),
                    $this->__('Trade Show')
                );
            }
            return $options;
        } else {
            if (Mage::getStoreConfig('shipping/wsafreightcommon/default_address', Mage::app()->getStore())) {
                $options = array(
                    $this->__('Business'),
                    $this->__('Residential'),
                );
            } else {
                $options = array(
                    $this->__('Residential'),
                    $this->__('Business')
                );
            }
            return $options;
        }
    }

    public function _getStepCodes()
    {
        return array('login', 'billing', 'shipping', 'shippingextra', 'shipping_method', 'payment', 'review');
    }

    public function hasFreightItems($items)
    {

        $globShipFreightClassPresent = Mage::getStoreConfig('shipping/wsafreightcommon/ship_freight_class_present');
        $useParent = Mage::getStoreConfig('shipping/wsafreightcommon/use_parent');

        foreach ($items as $item) {

            $product = Mage::helper('wsacommon/shipping')->getProduct($item, $useParent);

            $freightClass = $product->getData('freight_class');
            $fedexClass = $product->getData('fedex_freight_class');
            $freightClassSelect = $product->getData('freight_class_select');
            $prodMustShipFreight = $product->getData('must_ship_freight');

            if ($prodMustShipFreight || ($freightClass != "" || $fedexClass != "" || $freightClassSelect != "") && $globShipFreightClassPresent) {
                return true;
            }

        }
        return false;
    }


    public function isNotifyOptionEnabled()
    {

        $enabledCarriers = $this->getAllFreightCarriers();

        $applicableCarriers = array('cerasisfreight', 'estesfreight', 'echofreight');

        foreach ($applicableCarriers as $carrier) {

            if (in_array($carrier, $enabledCarriers)) {
                return true;
            } else {
                continue;
            }
        }
        return false;
    }

    public function isInsideDeliveryEnabled()
    {

        $enabledCarriers = $this->getAllFreightCarriers();

        $applicableCarriers = array('cerasisfreight', 'estesfreight');

        foreach ($applicableCarriers as $carrier) {

            if (in_array($carrier, $enabledCarriers)) {
                return true;
            } else {
                continue;
            }
        }

        return false;
    }

    public function setDateOffset($todaysDate, $carrier)
    {

        $blackoutDeliveryDates = Mage::getStoreConfig('carriers/' . $carrier . '/delivery_dates');

        if (!empty($blackoutDeliveryDates)) {
            $blackoutDates = explode(",", $blackoutDeliveryDates);

            foreach ($blackoutDates as $dates) {

                $dates = str_replace("/", "", $dates);

                $year = substr($dates, -4);
                $month = substr($dates, 0, -6);
                $day = substr($dates, 2, -4);

                $changeDate = $year . $month . $day;

                if ($changeDate == $todaysDate) {
                    $todaysDate = date('Ymd', time() + 259200);
                    break;
                }
            }
        }
        return $todaysDate;
    }


    public function limitCarriersBasedOnFreightRules($request, $limitCarrier)
    {

        if (count($this->getAllFreightCarriers())<1) {
            return $limitCarrier;  // no currently active freight carriers
        }
        $restrictRates = Mage::getStoreConfig('shipping/wsafreightcommon/restrict_rates');
        $forceFreight = Mage::getStoreConfig('shipping/wsafreightcommon/force_freight');
        $hasFreightItems = $this->hasFreightItems($request->getAllItems());
        $alwaysShowCarriersArr = explode(',', Mage::getStoreConfig('shipping/wsafreightcommon/show_carriers'));
        $allFreightCarriers = $this->getAllFreightCarriers();
        if (($restrictRates && $request->getPackageWeight() >= Mage::getStoreConfig('shipping/wsafreightcommon/min_weight')) ||
            ($forceFreight && $hasFreightItems)
        ) {
            if (!$limitCarrier) {
                $limitCarrier = array();

            } else {
                if (!is_array($limitCarrier)) {
                    $limitCarrier = array($limitCarrier);
                }
            }

            if (!empty($alwaysShowCarriersArr)) {
                foreach ($alwaysShowCarriersArr as $showCarrierCode) {
                    $limitCarrier[] = $showCarrierCode;
                }
            }

            foreach ($allFreightCarriers as $limit) {
                $limitCarrier[] = $limit;
            }

        } else if ($request->getPackageWeight() < Mage::getStoreConfig('shipping/wsafreightcommon/min_weight') && !$hasFreightItems) {
            if (!$limitCarrier) {
                $carriers = Mage::getStoreConfig('carriers', $request->getStoreId());
                foreach ($carriers as $carrierCode => $carrierConfig) {
                    if (in_array($carrierCode, $allFreightCarriers)) {
                        continue;
                    }
                    $limitCarrier[] = $carrierCode;
                }

            } else {
                if (!is_array($limitCarrier)) {
                    $limitCarrier = array($limitCarrier);
                }
                foreach ($limitCarrier as $carrierCode => $carrierConfig) {
                    if (in_array($carrierCode, $allFreightCarriers)) {
                        continue;
                    }
                    $limitCarrier[] = $carrierCode;
                }
            }

        }
        return $limitCarrier;
    }

}
