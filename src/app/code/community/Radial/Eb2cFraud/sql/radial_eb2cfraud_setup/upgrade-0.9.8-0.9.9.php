<?php
/**
 * Copyright (c) 2013-2016 Radial, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright   Copyright (c) 2013-2016 Radial, Inc. (http://www.radial.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Add fields to quote/ order for JSC 41st Parameter and associated orderContext fields
 *
 * @var Mage_Sales_Model_Resource_Setup $installer
 */
$installer = $this;
$installer->startSetup();

// Get status model
$status = Mage::getModel('sales/order_status');

//Add a new status
$status->setStatus('risk_ready_to_ship_wo_tax')
       ->setLabel('Ready to Ship Without Tax')
       ->assignState(Mage_Sales_Model_Order::STATE_PROCESSING)
       ->save();

$installer->endSetup();
