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

class Radial_Eb2cFraud_Model_Adminhtml_System_Config_Backend_Nummaxretry extends Mage_Core_Model_Config_Data
{
    public function _afterLoad()
    {
	$maxretries = Mage::helper('radial_eb2cfraud/config')->getMaxRetries();
	$objectCollectionSize = Mage::getModel('radial_core/retryQueue')->getCollection()
					->addFieldToFilter('delivery_status', $maxretries)
					->addFieldToFilter(
				        	array('event_name', 'event_name'),
                                                        array(array('eq' => 'risk_assessment_request'), array('eq' => 'order_confirmation_request'))
					)->getSize();
	
	$publicDisplay = '# of Fraud Messages At Max Transmission Retries: '. $objectCollectionSize;

        $this->setValue($publicDisplay);
        return $this;
    }

    /**
     * Simply turn of data saving. This is a display-only field in admin dependent upon presence of
     *  files in the filesystem, not any configuration data.
     * @return self
     * @codeCoverageIgnore Magento promises to not save anything if _dataSaveAllowed is false.
     */
    public function _beforeSave()
    {
        $this->_dataSaveAllowed = false;
        return $this;
    }
}
