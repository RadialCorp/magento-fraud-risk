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

class Radial_Eb2cFraud_Model_Observer extends Radial_Eb2cFraud_Model_Abstract
{
	/** @var Radial_Eb2cFraud_Helper_Data */
	protected $_helper;
	/** @var Radial_Eb2cFraud_Helper_Config */
	protected $_config;
	/** @var Radial_RiskInsight_Model_Risk_Order */
	protected $_riskOrder;

	/**
	 * @param array $initParams optional keys:
	 *                          - 'helper' => Radial_Eb2cFraud_Helper_Data
	 *                          - 'config' => Radial_Eb2cFraud_Helper_Config
	 */
	public function __construct(array $initParams=array())
	{
		$this->_riskOrder = Mage::getModel('radial_eb2cfraud/risk_order');

		list($this->_helper, $this->_config) = $this->_checkTypes(
			$this->_nullCoalesce($initParams, 'helper', Mage::helper('radial_eb2cfraud')),
			$this->_nullCoalesce($initParams, 'config', Mage::helper('radial_eb2cfraud/config'))
		);
	}

	/**
	 * Type checks for self::__construct $initParams
	 *
	 * @param  Radial_Eb2cFraud_Helper_Data
	 * @param  Radial_Eb2cFraud_Helper_Config
	 * @return array
	 */
	protected function _checkTypes(
		Radial_Eb2cFraud_Helper_Data $helper,
		Radial_Eb2cFraud_Helper_Config $config
	) {
		return array($helper, $config);
	}
	/**
	 * Return the value at field in array if it exists. Otherwise, use the
	 * default value.
	 *
	 * @param array      $arr
	 * @param string|int $field Valid array key
	 * @param mixed      $default
	 * @return mixed
	 */
	protected function _nullCoalesce(array $arr, $field, $default)
	{
		return isset($arr[$field]) ? $arr[$field] : $default;
	}

	/**
	 * @param  mixed
	 * @return bool
	 */
	protected function _isValidOrder($order=null)
	{
		return ($order && $order instanceof Mage_Sales_Model_Order);
	}

	/**
	 * Handle all orders.
	 *
	 * @param  Varien_Event_Observer
	 * @return self
	 */
	public function handleCheckoutSubmitAllAfter(Varien_Event_Observer $observer)
	{
                $quote = $observer->getEvent()->getQuote();

                if( !$quote->getIsMultiShipping())
                {
                        $orderI = $observer->getEvent()->getOrder()->getIncrementId();
			$order = Mage::getModel('sales/order')->loadByIncrementId($orderI);

			if( !$this->_config->isEnabled($order->getStoreId()))
                       	{
                       	        Mage::Log("For Order ID: ". $order->getIncrementId() . " Risk Service Module Has Been Disabled. Please go to System->Configuration->Payments,TDF, Fraud->Fraud->Enabled and toggled to 'YES'");
				return $this;
			}

                        if ($this->_isValidOrder($order)) {
                                $this->_riskOrder->processRiskOrder($order, $observer);
                        } else {
                                $logMessage = sprintf('[%s] No sales/order instances was found.', __CLASS__);
                                $this->_helper->logWarning($logMessage);
                        }
                } else {
                        $orderIds = Mage::getSingleton('core/session')->getOrderIds();
                        foreach( $orderIds as $orderId )
                        {
                                $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

				if( !$this->_config->isEnabled($order->getStoreId()))
                        	{       
                                	Mage::Log("For Order ID: ". $order->getIncrementId() . " Risk Service Module Has Been Disabled. Please go to System->Configuration->Payments,TDF, Fraud->Fraud->Enabled and toggled to 'YES'");
                                	continue;
                        	}  

                                if($this->_isValidOrder($order)) {
                                        $this->_riskOrder->processRiskOrder($order, $observer, $orderIds);
                                } else {
                                        $logMessage = sprintf('[%s] No sales/order instances was found.', __CLASS__);
                                        $this->_helper->logWarning($logMessage);
                                }
                        }
                }
                return $this;
        }

	/**
	  * Log Removed Cart Items
          * @param   Varien_Event_Observer
	  * @return  self
	  */
	public function addRemovedCartCount(Varien_Event_Observer $observer )
	{
		$previous = 0;
		$previous = Mage::getSingleton('core/session')->getPrevItemQuoteRemoval();
		$previous++;
		
		Mage::getSingleton('core/session')->setPrevItemQuoteRemoval($previous);
	}

	 /**
          * Log Auth Attempts in a Session, Reset when successful
          * @param   none
          * @return  self
          */
        public function countAuthAttempts()
        {
                $previous = Mage::getSingleton('core/session')->getCCAttempts();

                if(!$previous)
                {
                        $previous = 1;
                } else {
                        $previous++;
                }

                Mage::getSingleton('core/session')->setCCAttempts($previous);
        }
	
	public function updateOrderStatus(Varien_Event_Observer $observer)
        {
            $event = $observer->getEvent()->getPayload();

            $orderId = $event->getCustomerOrderId();
            $responseCode = $event->getResponseCode();
            $reasonCode = $event->getReasonCode();
            $reasonCodeDesc = $event->getReasonCodeDescription();
            $comment = "Fraud Response Code: ". $responseCode. "\n";

            if($reasonCode)
            {
                $comment .= "Fraud Reason Code: ". $reasonCode;

                if($reasonCodeDesc)
                {
                        $comment .= "\nFraud Reason Code Description: ". $reasonCodeDesc;
                }
            }

            $order = Mage::getModel("sales/order")->loadByIncrementId($orderId);

            if( $order->getId() )
            {
                if( !$this->_config->isEnabled($order->getStoreId()))
                {
                        Mage::Log("For Order ID: ". $order->getIncrementId() . " Risk Service Module Has Been Disabled. Please go to System->Configuration->Payments,TDF, Fraud->Fraud->Enabled and toggled to 'YES'");
                        return $this;
                }

                $addlInfo = $order->getPayment()->getAdditionalInformation();
                $riskResponseCode = $addlInfo['risk_response_code'];

                if( strcmp($riskResponseCode, 'DECLR') === 0 )
                {
                        $order->cancel();
                        $order->setState($this->_config->getOrderStateForResponseCode($responseCode), $this->_config->getOrderStatusForResponseCode($responseCode), $comment, false);
                        $order->addStatusHistoryComment("Order Auto-Canceled by Webstore: Card Reported Stolen");
                        $order->save();

                        return $this;
                }

                $status = $order->getStatus();
                $state = $order->getState();

                $accept = [ "risk_submitted", "risk_processing", "risk_rejectpending", "risk_suspend", "risk_ignore", "risk_accept", "risk_ready_to_ship_wo_tax" ];

                if( in_array( $status, $accept))
                {
                        if( $this->_config->getOrderStateForResponseCode($responseCode) === Mage_Sales_Model_Order::STATE_CANCELED )
                        {
                                $order->cancel();
                                $order->addStatusHistoryComment($comment);
                        } else {
                                $order->setState($this->_config->getOrderStateForResponseCode($responseCode), $this->_config->getOrderStatusForResponseCode($responseCode), $comment, false);
                        }

                        $order->save();

                        if( strcmp($this->_config->getOrderStatusForResponseCode($responseCode), "risk_accept") === 0)
                        {
                                Mage::Log("Risk Accept Sent");

                                if( Mage::getStoreConfig('radial_core/radial_tax_core/enabledmod', $order->getStoreId()) && $order->getData('radial_tax_transmit') != -1 )
                                {
                                        $order->setState($this->_config->getOrderStateForResponseCode($responseCode), "risk_ready_to_ship_wo_tax", "Fraud Accepted - But No Tax Calc ... DO NOT RELEASE ORDER!", false);
                                } else {
                                        Mage::Log("Dispatch Fraud Accept for Payments");

                                        Mage::dispatchEvent("radial_eb2cfraud_dispatch_fraud_accept", array('order' => $order));
                                }
                        }
                } else {
                        $order->addStatusHistoryComment($comment);
                        $order->save();
                }
            }

            return $this;
        }
}
