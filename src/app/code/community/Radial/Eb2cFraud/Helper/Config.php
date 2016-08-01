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
class Radial_Eb2cFraud_Helper_Config
{
	const ENABLED = 'radial_core/fraud/enabledmod';
	const STORE_ID = 'radial_core/general/store_id';
	const API_HOSTNAME = 'radial_core/api/hostname';
	const API_KEY = 'radial_core/api/key';
	const API_TIMEOUT = 'radial_core/api/timeout';
	const RESPONSE_TIMEOUT = 'radial_core/fraud/responsetimeout';
	const MAXRETRIES = 'radial_core/fraud/maxretries';
	const DEBUG = 'radial_core/fraud/debug';
	const FRAUD_EMAIL = 'radial_core/fraud/fraud_email';
	const LANGUAGE_CODE = 'radial_core/general/language_code';
        const CARD_TYPE_MAP = 'radial_eb2cfraud/risk_service/card_type_map';
	const CARD_NAME_MAP = 'radial_eb2cfraud/risk_service/card_name_map';
        const SHIPPING_METHOD_MAP = 'radial_eb2cfraud/risk_service/shipping_method_map';
        const PAYMENT_ADAPTER_MAP = 'radial_eb2cfraud/risk_service/payment_adapter_map';
	const FRAUD_RESPONSE_CODE_MAP = 'radial_eb2cfraud/risk_service/response_codes';
	const FRAUD_RESPONSE_CODE_STATES_MAP = 'radial_eb2cfraud/risk_service/response_codes_states';
	const ORDER_STATUS_MAP = 'radial_eb2cfraud/risk_service/order_status_codes_to_fraud';
	const ITEM_STATUS_MAP = 'radial_eb2cfraud/risk_service/item_status_codes_to_fraud';
	const ORDER_STATUS_CONFIRMATION_MAP = 'radial_eb2cfraud/risk_service/order_status_to_confirmation_codes';
	const SHIP_VENDOR_MAP = 'radial_eb2cfraud/risk_service/ship_vendor_to_fraud';
	const UOM = 'radial_eb2cfraud/risk_service/uom';

	/**
	 * check if Risk Insight module is enable in the store config
	 *
	 * @param  mixed
	 * @return bool
	 */
	public function isEnabled($store=null)
	{
		return Mage::getStoreConfigFlag(static::ENABLED, $store);
	}

	/**
	 * check if debug mode is enable in the store config
	 *
	 * @param  mixed
	 * @return bool
	 */
	public function isDebugMode($store=null)
	{
		return Mage::getStoreConfigFlag(static::DEBUG, $store);
	}

	/**
	 * get maximum transmission retry attempts
	 *
	 * @param   mixed
	 * @return  integer
	 */
	public function getMaxRetries($store=null)
	{
		return Mage::getStoreConfig(static::MAXRETRIES, $store);
	}

	/**
	 * get fraud admin email
	 *
	 * @param   mixed
	 * @return  string
	 */
	public function getFraudEmail($store=null)
	{
		return Mage::getStoreConfig(static::FRAUD_EMAIL, $store);
	}

	/**
	 * retrieve the FraudNet Store id from store config
	 *
	 * @param  mixed
	 * @return string
	 */
	public function getStoreId($store=null)
	{
		return Mage::getStoreConfig(static::STORE_ID, $store);
	}

	/**
	 * retrieve the language code from store config
	 *
	 * @param  mixed
	 * @return string
	 */
	public function getLanguageCodeId($store=null)
	{
		return Mage::getStoreConfig(static::LANGUAGE_CODE, $store);
	}

	/**
	 * retrieve the API Host Name from store config
	 *
	 * @param  mixed
	 * @return string
	 */
	public function getApiHostname($store=null)
	{
		return Mage::getStoreConfig(static::API_HOSTNAME, $store);
	}

	/**
	 * retrieve the API encrypted key from store config and decrypt it.
	 *
	 * @param  mixed
	 * @return string
	 */
	public function getApiKey($store=null)
	{
		return Mage::getStoreConfig(static::API_KEY, $store);
	}

	/**
	 * retrieve the API timeout setting from store config
	 *
	 * @param  mixed
	 * @return string
	 */
	public function getApiTimeout($store=null)
	{
		return Mage::getStoreConfig(static::API_TIMEOUT, $store);
	}

    /**
     * retrieve the response timeout setting from store config
     *
     * @param  mixed
     * @return string
     */
    public function getResponseTimeout($store=null)
    {
            return Mage::getStoreConfig(static::RESPONSE_TIMEOUT, $store);
    }

    /**
     * retrieve the payment method card type map settings from store config
     *
     * @param  mixed
     * @return string
     */
    public function getPaymentMethodCardTypeMap($store=null)
    {
        return Mage::getStoreConfig(static::CARD_TYPE_MAP, $store);
    }

    /**
     * retrieve the payment method card type name settings from store config
     *
     * @param  mixed
     * @return string
     */
    public function getPaymentMethodCardNameMap($store=null)
    {
        return Mage::getStoreConfig(static::CARD_NAME_MAP, $store);
    }

    /**
     * retrieve the shipping method map settings from store config
     *
     * @param  mixed
     * @return string
     */
    public function getShippingMethodMap($store=null)
    {
        return Mage::getStoreConfig(static::SHIPPING_METHOD_MAP, $store);
    }

    /**
     * retrieve the shipping vendor map settings from store config
     *
     * @param  mixed
     * @return string
     */
    public function getShipVendorMap($store=null)
    {
        return Mage::getStoreConfig(static::SHIP_VENDOR_MAP, $store);
    }

    /**
     * retrieve the payment adapter map settings from store config
     *
     * @param  mixed
     * @return string
     */
    public function getPaymentAdapterMap($store=null)
    {
        return Mage::getStoreConfig(static::PAYMENT_ADAPTER_MAP, $store);
    }

    /**
     * retrieve the fraud reason code map settings from store config
     *
     * @param  mixed
     * @return string
     */
    public function getFraudResponseCodeMap($store=null)
    {
        return Mage::getStoreConfig(static::FRAUD_RESPONSE_CODE_MAP, $store);
    }

    /**
     * retrieve the fraud reason code map to order states from store config
     *
     * @param  mixed
     * @return string
     */
    public function getFraudResponseCodeStatesMap($store=null)
    {
        return Mage::getStoreConfig(static::FRAUD_RESPONSE_CODE_STATES_MAP, $store);
    }

    /**
     * retrieve the order status to fraud order status map settings from store config
     *
     * @param  mixed
     * @return string
     */
    public function getOrderStatusToFraudMap($store=null)
    {
	return Mage::getStoreConfig(static::ORDER_STATUS_MAP, $store);
    }

    /**
     * retrieve the item status to fraud item status map settings from store config
     *
     * @param  mixed
     * @return string
     */
    public function getItemStatusToFraudMap($store=null)
    {
        return Mage::getStoreConfig(static::ITEM_STATUS_MAP, $store);
    }

    /**
     * retrieve the unit of measure for the store
     *
     * @param  mixed
     * @return string
     */
    public function getUnitOfMeasure($store=null)
    {
	return Mage::getStoreConfig(static::UOM, $store);
    }

    /**
     * Get the ROM Tender Type for the Magento CC Type.
     * @param  string $creditCardType
     * @return string
     */
    public function getTenderTypeForCcType($creditCardType)
    {
        $types = $this->getPaymentMethodCardTypeMap();
        if (isset($types[$creditCardType])) {
            return $types[$creditCardType];
        }
        throw Mage::exception('Radial_Eb2cFraud', self::UNKNOWN_CARD_TYPE);
    }

   /**
    * Get Order State to OCR Confirmation Code Maps
    * @param  string $orderState
    * @return string
    */
   public function getOrderStateToConfirmationCodeMap($store=null)
   {
	return Mage::getStoreConfig(static::ORDER_STATUS_CONFIRMATION_MAP, $store);
   }

    /**
     * Get the Name for the Magento CC Type.
     * @param  string $creditCardType
     * @return string
     */
    public function getTenderNameForCcType($creditCardType)
    {
        $types = $this->getPaymentMethodCardNameMap();
        if (isset($types[$creditCardType])) {
            return $types[$creditCardType];
        }
    }

    /**
     * Get Order Status for Fraud ResponseCode.
     * @param  string $responseCode
     * @return string
     */
    public function getOrderStatusForResponseCode($responseCode)
    {
        $codes = $this->getFraudResponseCodeMap();
        if (isset($codes[$responseCode])) {
            return $codes[$responseCode];
        }

	throw Mage::exception('Radial_Eb2cFraud', "Unknown Fraud Response Code");
    }

    /**
     * Get Order State for Fraud ResponseCode.
     * @param  string $responseCode
     * @return string
     */
    public function getOrderStateForResponseCode($responseCode)
    {
        $codes = $this->getFraudResponseCodeStatesMap();
        if (isset($codes[$responseCode])) {
            return $codes[$responseCode];
        }

        throw Mage::exception('Radial_Eb2cFraud', "Unknown Fraud Response Code");
    }

   /**
    * Get Order State for Fraud OCR.
    * @param  string  $orderState
    * @return string
    */
   public function getOrderStateForFraudOCR($orderState)
   {
	$codes = $this->getOrderStatusToFraudMap();
	if (isset($codes[$orderState])) {
		return $codes[$orderState];
	} else {
		return "IN_PROCESS";
	}

	throw Mage::exception('Radial_Eb2cFraud', "Invalid Magento Order State!");
   }

   /**
    * Get Item State for Fraud OCR.
    * @param  string  $itemState
    * @return string
    */
   public function getItemStateForFraudOCR($itemState)
   {
        $codes = $this->getItemStatusToFraudMap();
        if (isset($codes[$itemState])) {
                return $codes[$itemState];
        }
   
        throw Mage::exception('Radial_Eb2cFraud', "Invalid Magento Item State!");
   }

   /**
    * Get Order State to Confirmation Code for Fraud OCR.
    * @param  string  $itemState
    * @return string
    */
   public function getOrderStateForConfirmationFraudOCR($orderState)
   {
        $codes = $this->getOrderStateToConfirmationCodeMap();
        if (isset($codes[$orderState])) {
                return $codes[$orderState];
        } else {
		return "OTHER";	
	}

        throw Mage::exception('Radial_Eb2cFraud', "Invalid Magento Order State!");
   }

   /**
    * Map Ship Carrier to Ship Vendor for Order Confirmation Request
    * @param  string  $shipCarrier
    * @return string
    */
   public function getShipVendorForShipCarrier($shipCarrier)
   {
	$codes = $this->getShipVendorMap();
        if (isset($codes[$shipCarrier])) {
                return $codes[$shipCarrier];
        } else {
		return "OTHER";
        }
   }
}
