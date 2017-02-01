<?php
/**
 * Copyright (c) 2015 eBay Enterprise, Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the eBay Enterprise
 * Magento Extensions End User License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * http://www.ebayenterprise.com/files/pdf/Magento_Connect_Extensions_EULA_050714.pdf
 *
 * @copyright   Copyright (c) 2015 eBay Enterprise, Inc. (http://www.ebayenterprise.com/)
 * @license     http://www.ebayenterprise.com/files/pdf/Magento_Connect_Extensions_EULA_050714.pdf  eBay Enterprise Magento Extensions End User License Agreement
 *
 */
class Radial_Eb2cFraud_Model_Payment_Adapter_Default
	extends Radial_Eb2cFraud_Model_Payment_Adapter_Type
{
	protected function _initialize()
	{
		$payment = $this->_order->getPayment();
		$this->setExtractCardHolderName($payment->getCcOwner())
			->setExtractPaymentAccountUniqueId($this->_helper->getAccountUniqueId($payment))
			->setExtractIsToken(static::IS_TOKEN)
			->setExtractPaymentAccountBin($this->_helper->getAccountBin($payment))
			->setExtractExpireDate($this->_helper->getPaymentExpireDate($payment))
			->setExtractCardType($this->_helper->getMapRiskInsightPaymentMethod($payment))
			->setExtractTransactionResponses(array());
		return $this;
	}
}
