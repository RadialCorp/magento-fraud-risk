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

/**
 * @codeCoverageIgnore
 */
class Radial_Eb2cFraud_Model_Payment_Adapter_Type
	extends Radial_Eb2cFraud_Model_Payment_Adapter_Type_Abstract
	implements Radial_Eb2cFraud_Model_Payment_Adapter_IType
{
	/** @var Mage_Sales_Model_Order */
	protected $_order;
	/** @var Radial_Eb2cFraud_Helper_Data */
	protected $_helper;
	/** @var Radial_Eb2cFraud_Helper_Config */
	protected $_config;
	/** @var string | null */
	protected $_extractCardHolderName;
	/** @var string | null */
	protected $_extractPaymentAccountUniqueId;
	/** @var string | null */
	protected $_extractPaymentAccountBin;
	/** @var string | null */
	protected $_extractExpireDate;
	/** @var string | null */
	protected $_extractCardType;
	/** @var string | null */
	protected $_extractTransactionResponses;
	/** @var string */
	protected $_extractIsToken;

	/**
	 * @param array $initParams Must have this key:
	 *                          - 'order' => Mage_Sales_Model_Order
	 *                          - 'helper' => Radial_Eb2cFraud_Helper_Data
	 *			    - 'config' => Radial_Eb2cFraud_Helper_Config
	 */
	public function __construct(array $initParams=array())
	{
		list($this->_order, $this->_helper, $this->_config) = $this->_checkTypes(
			$this->_nullCoalesce($initParams, 'order', $initParams['order']),
			$this->_nullCoalesce($initParams, 'helper', Mage::helper('radial_eb2cfraud')),
			$this->_nullCoalesce($initParams, 'config', Mage::helper('radial_eb2cfraud/config'))
		);
		$this->_initialize();
	}

	/**
	 * Type hinting for self::__construct $initParams
	 *
	 * @param  Mage_Sales_Model_Order
	 * @param  Radial_Eb2cFraud_Helper_Data
	 * @param  Radial_Eb2cFraud_Helper_Config
	 * @return array
	 */
	protected function _checkTypes(
		Mage_Sales_Model_Order $order,
		Radial_Eb2cFraud_Helper_Data $helper,
		Radial_Eb2cFraud_Helper_Config $config
	) {
		return array($order, $helper, $config);
	}

	/**
	 * Return the value at field in array if it exists. Otherwise, use the default value.
	 *
	 * @param  array
	 * @param  string | int $field Valid array key
	 * @param  mixed
	 * @return mixed
	 */
	protected function _nullCoalesce(array $arr, $field, $default)
	{
		return isset($arr[$field]) ? $arr[$field] : $default;
	}

	public function getExtractCardHolderName()
	{
		return $this->_extractCardHolderName;
	}

	public function setExtractCardHolderName($cardHolderName)
	{
		$this->_extractCardHolderName = $cardHolderName;
		return $this;
	}

	public function getExtractPaymentAccountUniqueId()
	{
		return $this->_extractPaymentAccountUniqueId;
	}

	public function setExtractPaymentAccountUniqueId($paymentAccountUniqueId)
	{
		$this->_extractPaymentAccountUniqueId = $paymentAccountUniqueId;
		return $this;
	}

	public function getExtractPaymentAccountBin()
	{
		return $this->_extractPaymentAccountBin;
	}

	public function setExtractPaymentAccountBin($paymentAccountBin)
	{
		$this->_extractPaymentAccountBin = $paymentAccountBin;
		return $this;
	}

	public function getExtractExpireDate()
	{
		return $this->_extractExpireDate;
	}

	public function setExtractExpireDate($expireDate)
	{
		$this->_extractExpireDate = $expireDate;
		return $this;
	}

	public function getExtractCardType()
	{
		return $this->_extractCardType;
	}

	public function setExtractCardType($cardType)
	{
		$this->_extractCardType = $cardType;
		return $this;
	}

	public function getExtractTransactionResponses()
	{
		return $this->_extractTransactionResponses;
	}

	public function setExtractTransactionResponses(array $transactionResponses)
	{
		$this->_extractTransactionResponses = $transactionResponses;
		return $this;
	}

	public function getExtractIsToken()
	{
		return $this->_extractIsToken;
	}

	public function setExtractIsToken($isToken)
	{
		$this->_extractIsToken = $isToken;
		return $this;
	}

	protected function _initialize()
	{
		return $this;
	}
}
