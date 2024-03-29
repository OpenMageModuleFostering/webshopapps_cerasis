<?php

/**
 * WebShopApps
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
 * @category    WebShopApps
 * @package     WebShopApps Wsafreightcommon
 * @copyright   Copyright (c) 2012 Zowta Ltd (http://www.webshopapps.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$freightCarriers = array ('yrcfreight','wsaupsfreight','estesfreight','echofreight','abffreight','conwayfreight','wsafedexfreight','rlfreight','ctsfreight','dmtrans');

foreach ($freightCarriers as $carrier) {

    $rows = $installer->_conn->fetchAll("select * from {$this->getTable('core_config_data')} where path in ('carriers/$carrier/use_parent')");
    $search = array('carriers',$carrier);
    $replace = array ('shipping','wsafreightcommon');

    foreach ($rows as $r) {
        $r['path'] = str_replace($search,$replace,$r['path']);
        $installer->_conn->update($this->getTable('core_config_data'), $r, 'config_id='.$r['config_id']);
    }
}


$installer->endSetup();
