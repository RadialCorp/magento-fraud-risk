<?php
/**
 * Copyright (c) 2015 Radial, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Radial
 * Magento Extensions End User License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * http://www.radial.com/files/pdf/Magento_Connect_Extensions_EULA_050714.pdf
 *
 * @copyright   Copyright (c) 2015 Radial, Inc. (http://www.radial.com/)
 * @license     http://www.radial.com/files/pdf/Magento_Connect_Extensions_EULA_050714.pdf  Radial Magento Extensions End User License Agreement
 *
 */

class Radial_Eb2cFraud_Model_Payment_Adapter_Paypal_Express
	extends Radial_Eb2cFraud_Model_Payment_Adapter_Type
{
	protected function _initialize()
	{
		$payment = $this->_order->getPayment();
		$owner = $this->_order->getBillingAddress()->getName();
		$this->setExtractCardHolderName($owner)
			->setExtractPaymentAccountUniqueId($this->_getExtractPaymentAccountUniqueId($payment))
			->setExtractIsToken(static::IS_NOT_TOKEN)
			->setExtractPaymentAccountBin(null)
			->setExtractExpireDate(null)
			->setExtractCardType("PAYPAL")
			->setExtractTransactionResponses($this->_getPaypalTransactions($payment));
		return $this;
	}

	/**
	 * @return Mage_Paypal_Model_Info
	 */
	protected function _getInfoInstance()
	{
		return Mage::getModel('paypal/info');
	}

	/**
	 * @param  Mage_Payment_Model_Info
	 * @return Mage_Payment_Model_Method_Cc
	 */
	protected function _getPaypalInfo(Mage_Payment_Model_Info $payment)
	{
		return $payment->getAdditionalInformation();
	}

	/**
	 * @param  Mage_Payment_Model_Info
	 * @return string | null
	 */
	protected function _getExtractPaymentAccountUniqueId(Mage_Payment_Model_Info $payment)
	{
		$info = $this->_getPaypalInfo($payment);
		return isset($info['paypal_express_checkout_payer_id']) ? $info['paypal_express_checkout_payer_id'] : null;
	}

	/**
	 * @param  Mage_Payment_Model_Info
	 * @return array
	 */
	protected function _getPaypalTransactions(Mage_Payment_Model_Info $payment)
	{
		$info = $this->_getPaypalInfo($payment);
		$transArray = array();

                if( isset($info['paypal_express_checkout_address_status']))
                {
                        $transArray[] = array('type' => 'PayPalAddress', 'response' => strtolower($info['paypal_express_checkout_address_status']));
                }

                if( isset($info['paypal_express_checkout_payer_status']))
                {
                        $transArray[] = array('type' => 'PayPalPayer', 'response' => strtolower($info['paypal_express_checkout_payer_status']));
                }

                return $transArray;
	}
}
