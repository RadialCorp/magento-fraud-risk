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

interface Radial_Eb2cFraud_Model_Build_IRequest
{
    const RESPONSE_TYPE = 'avs';
    const DEFAULT_SHIPPING_METHOD ='Unknown';
    const PHYSICAL_SHIPMENT_TYPE = 'physical';
    const VIRTUAL_SHIPMENT_TYPE = 'virtual';
    const VIRTUAL_SHIPPING_METHOD = 'EMAIL';

    /**
     * Build the Risk Service request payload.
     *
     * @return Radial_Eb2cFraud_Sdk_IPayload
     */
    public function build();
}
