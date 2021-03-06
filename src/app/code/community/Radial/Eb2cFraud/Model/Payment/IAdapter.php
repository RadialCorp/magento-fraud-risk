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

interface Radial_Eb2cFraud_Model_Payment_IAdapter
{
	const DEFAULT_ADAPTER = 'radial_eb2cfraud/payment_adapter_default';
	const GIFT_CARD_PAYMENT_METHOD = 'giftcard';

	/**
	 * @return Radial_Eb2cFraud_Model_Payment_Adapter_IType
	 */
	public function getAdapter();
}
