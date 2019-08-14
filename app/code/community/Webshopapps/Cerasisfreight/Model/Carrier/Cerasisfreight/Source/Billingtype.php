<?php
/* Cerasisfreight Shipping
 *
 * @category   Webshopapps
 * @package    Webshopapps_Cerasisfreight
 * @copyright  Copyright (c) 2012 Zowta Ltd (http://www.webshopapps.com)
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

class Webshopapps_Cerasisfreight_Model_Carrier_Cerasisfreight_Source_Billingtype {
	
public function toOptionArray()
    {
        $cerasisfreight = Mage::getSingleton('cerasisfreight/carrier_cerasisfreight');
        $arr = array();
        foreach ($cerasisfreight->getCode('billing_type') as $k=>$v) {
            $arr[] = array('value'=>$k, 'label'=>Mage::helper('usa')->__($v));
        }
        return $arr;
    }
}
