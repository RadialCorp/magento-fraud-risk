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

interface EbayEnterprise_RiskService_Sdk_IPayment extends EbayEnterprise_RiskService_Sdk_IPayload
{
	const ROOT_NODE = 'FormOfPayment';
	const XML_NS = 'http://api.gsicommerce.com/schema/checkout/1.0';
	const PAYMENT_CARD_MODEL ='EbayEnterprise_RiskService_Sdk_Payment_Card';
	const PERSON_NAME_MODEL ='EbayEnterprise_RiskService_Sdk_Person_Name';
	const TELEPHONE_MODEL ='EbayEnterprise_RiskService_Sdk_Telephone';
	const ADDRESS_MODEL ='EbayEnterprise_RiskService_Sdk_Address';
	const TRANSACTION_RESPONSES_MODEL ='EbayEnterprise_RiskService_Sdk_Transaction_Responses';
	const AUTHORIZATION_MODEL = 'EbayEnterprise_RiskService_Sdk_Authorization';

	/**
	 * Contains credit card and card holder information.
	 *
	 * @return EbayEnterprise_RiskService_Sdk_Payment_ICard
	 */
	public function getPaymentCard();

	/**
	 * @param  EbayEnterprise_RiskService_Sdk_Payment_ICard
	 * @return self
	 */
	public function setPaymentCard(EbayEnterprise_RiskService_Sdk_Payment_ICard $paymentCard);

	/**
         * Contains credit card and card holder information.
         *
         * @return EbayEnterprise_RiskService_Sdk_IAuthorization
         */
        public function getAuthorization();

        /**
         * @param  EbayEnterprise_RiskService_Sdk_IAuthorization
         * @return self
         */
        public function setAuthorization(EbayEnterprise_RiskService_Sdk_IAuthorization $authorization);

	/**
	 * @return DateTime
	 */
	public function getPaymentTransactionDate();

	/**
	 * @param  DateTime
	 * @return self
	 */
	public function setPaymentTransactionDate(DateTime $paymentTransactionDate);

	/**
         * @return int
         */
        public function getPaymentTransactionID();

        /**
         * @param  int
         * @return self
         */
        public function setPaymentTransactionID($paymentTransactionID);

	/**
         * @return string
         */
        public function getPaymentTransactionTypeCode();

        /**
         * @param  string
         * @return self
         */
        public function setPaymentTransactionTypeCode($paymentTransactionTypeCode);

	/**
         * @return integer
         */
        public function getItemListRPH();
         
        /**
         * @param  integer
         * @return self
         */
        public function setItemListRPH($itemListRPH);

	/**
         * @return string
         */
        public function getAccountID();

        /**
         * @param  string
         * @return self
         */
        public function setAccountID($accountID);

	 /**
         * @return boolean
         */
        public function getIsToken();
         
        /**
         * @param  boolean
         * @return self
         */
        public function setIsToken($isToken);

	/**
	 * @return string
	 */
	public function getCurrencyCode();

	/**
	 * @param  string
	 * @return self
	 */
	public function setCurrencyCode($currencyCode);

	/**
	 * @return float
	 */
	public function getAmount();

	/**
	 * @param  float
	 * @return self
	 */
	public function setAmount($amount);

	/**
	 * Contains the list of responses from the payment processor.
	 *
	 * @return EbayEnterprise_RiskService_Sdk_Transaction_IResponses
	 */
	public function getTransactionResponses();

	/**
	 * @param  EbayEnterprise_RiskService_Sdk_Transaction_IResponses
	 * @return self
	 */
	public function setTransactionResponses(EbayEnterprise_RiskService_Sdk_Transaction_IResponses $transactionResponses);
}
