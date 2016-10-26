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
$entities = array('order', 'quote');

$typeTextOptions = array (
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'visible' => true,
    'required' => false,
);

$fraudAttributes = array(
    array( 'name' => 'char_set',        'options' => $typeTextOptions),
    array( 'name' => 'content_types',   'options' => $typeTextOptions),
    array( 'name' => 'encoding',        'options' => $typeTextOptions),
    array( 'name' => 'host_name',       'options' => $typeTextOptions),
    array( 'name' => 'ip_address',      'options' => $typeTextOptions),
    array( 'name' => 'javascript_data',     'options' => $typeTextOptions),
    array( 'name' => 'language',        'options' => $typeTextOptions),
    array( 'name' => 'referrer',        'options' => $typeTextOptions),
    array( 'name' => 'session_id',      'options' => $typeTextOptions),
    array( 'name' => 'user_agent',      'options' => $typeTextOptions),
);

$pfx = 'eb2c_fraud_';

foreach ($entities as $entity) {
    foreach ($fraudAttributes as $a) {
        $installer->addAttribute($entity, $pfx . $a['name'], $a['options']);
    }
}

// Get status model
$status = Mage::getModel('sales/order_status');

// Delete some statuses
$status->setStatus('risk_accept')->delete();
$status->setStatus('risk_processing')->delete();
$status->setStatus('risk_accept')->delete();
$status->setStatus('risk_cancel')->delete();
$status->setStatus('risk_ignore')->delete();
$status->setStatus('risk_suspend')->delete();
$status->setStatus('risk_rejectpending')->delete();
$status->setStatus('risk_submitted')->delete();

//Add a new status
$status->setStatus('risk_accept')
       ->setLabel('Fraud Accepted')
       ->assignState(Mage_Sales_Model_Order::STATE_PROCESSING)
       ->save();

//Add a new status
$status->setStatus('risk_submitted')
       ->setLabel('Order Submitted to Fraud System')
       ->assignState('pending')
       ->save();

//Add a new status
$status->setStatus('risk_processing')
       ->setLabel('Order Under Review for Fraud Detection')
       ->assignState(Mage_Sales_Model_Order::STATE_PROCESSING)
       ->save();

//Add a new status
$status->setStatus('risk_cancel')
       ->setLabel('Fraud Cancelled')
       ->assignState(Mage_Sales_Model_Order::STATE_CANCELED)
       ->save();

//Add a new status
$status->setStatus('risk_ignore')
       ->setLabel('Fraud Ignore')
       ->assignState(Mage_Sales_Model_Order::STATE_PROCESSING)
       ->save();

//Add a new status
$status->setStatus('risk_suspend')
       ->setLabel('Fraud Suspend')
       ->assignState(Mage_Sales_Model_Order::STATE_PROCESSING)
       ->save();

//Add a new status
$status->setStatus('risk_rejectpending')
       ->setLabel('Fraud Reject Pending')
       ->assignState(Mage_Sales_Model_Order::STATE_PROCESSING)
       ->save();

//Add a new status
$status->setStatus('risk_retrysubmit')
       ->setLabel('Fraud Retry Submit')
       ->assignState(Mage_Sales_Model_Order::STATE_PROCESSING)
       ->save();

$status->setStatus('risk_ready_to_ship_wo_tax')
       ->setLabel('Ready to Ship Without Tax')
       ->assignState(Mage_Sales_Model_Order::STATE_PROCESSING)
       ->save();

$installer->endSetup();
