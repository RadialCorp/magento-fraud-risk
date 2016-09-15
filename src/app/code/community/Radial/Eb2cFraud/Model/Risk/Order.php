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

class Radial_Eb2cFraud_Model_Risk_Order
	extends Radial_Eb2cFraud_Model_Abstract
	implements Radial_Eb2cFraud_Model_Risk_IOrder
{
	/** @var Radial_Eb2cFraud_Helper_Data */
	protected $_helper;
	/** @var Radial_Eb2cFraud_Helper_Config */
	protected $_config;
	/** @var Radial_Eb2cFraud_Helper_Http */
	protected $_httpHelper;
    	/** @var Radial_RiskService_Sdk_Request */
    	protected $_request;
	/** @var Radial_RiskService_Sdk_OrderConfirmationRequest */
	protected $_OCrequest;
	/** @var Radial_RiskService_Sdk_Response */
	protected $_response;
	/** @var EbayEnterprise_MageLog_Helper_Data */
    	protected $_logger;
	/** @var EbayEnterprise_MageLog_Helper_Context */
   	protected $_context;
	/** @var payload message **/
	protected $_payloadXml;
	
	/**
	 * @param array $initParams optional keys:
	 *                          - 'helper' => Radial_Eb2cFraud_Helper_Data
	 *			    - 'httpHelper' => Radial_Eb2cFraud_Helper_Http
	 *                          - 'config' => Radial_Eb2cFraud_Helper_Config
	 */
	public function __construct(array $initParams=array())
	{

		list($this->_helper, $this->_httpHelper, $this->_request, $this->_config, $this->_response, $this->_logger, $this->_context, $this->_OCrequest) = $this->_checkTypes(
			$this->_nullCoalesce($initParams, 'helper', Mage::helper('radial_eb2cfraud')),
			$this->_nullCoalesce($initParams, 'http_helper', Mage::helper('radial_eb2cfraud/http')),
		        $this->_nullCoalesce($initParams, 'request', $this->_getNewSdkInstance('Radial_RiskService_Sdk_Request')),
			$this->_nullCoalesce($initParams, 'config', Mage::helper('radial_eb2cfraud/config')),
			$this->_nullCoalesce($initParams, 'response', $this->_getNewSdkInstance('Radial_RiskService_Sdk_Response')),
			$this->_nullCoalesce($initParams, 'logger', Mage::helper('ebayenterprise_magelog')),
			$this->_nullCoalesce($initParams, 'context', Mage::helper('ebayenterprise_magelog/context')),
			$this->_nullCoalesce($initParams, 'ocrequest', $this->_getNewSdkInstance('Radial_RiskService_Sdk_OrderConfirmationRequest'))
		);
	}

	/**
	 * Type hinting for self::__construct $initParams
	 *
	 * @param  Radial_Eb2cFraud_Helper_Data
	 * @param  Radial_Eb2cFraud_Helper_Http
	 * @param  Radial_Eb2cFraud_Helper_Config
	 * @param  EbayEnterprise_MageLog_Helper_Data
	 * @param  EbayEnterprise_MageLog_Helper_Context
	 * @return array
	 */
	protected function _checkTypes(
		Radial_Eb2cFraud_Helper_Data $helper,
		Radial_Eb2cFraud_Helper_Http $httpHelper,
        Radial_RiskService_Sdk_Request $request,
		Radial_Eb2cFraud_Helper_Config $config,
		Radial_RiskService_Sdk_Response $response,
		EbayEnterprise_MageLog_Helper_Data $logger,
		EbayEnterprise_MageLog_Helper_Context $context,
		Radial_RiskService_Sdk_OrderConfirmationRequest $OCrequest
	) {
		return array($helper, $httpHelper, $request, $config, $response, $logger, $context, $OCrequest);
	}

    /**
         * Get new API config object.
         *
         * @param  Radial_RiskService_Sdk_IPayload
         * @param  Radial_RiskService_Sdk_IPayload
         * @return Radial_RiskService_Sdk_IConfig
         */
        protected function _setupApiConfig(
                Radial_RiskService_Sdk_IPayload $request,
                Radial_RiskService_Sdk_IPayload $response
        )
        {
                return $this->_getNewSdkInstance('Radial_RiskService_Sdk_Config', array(
                        'api_key' => $this->_config->getApiKey(),
                        'host' => $this->_config->getApiHostname(),
                        'store_id' => $this->_config->getStoreId(),
			'responsetimeout' => $this->_config->getResponseTimeout(),
                        'request' => $request,
                        'response' => $response,
                ));
        }

	/**
         * Get new API object.
         *
         * @param  Radial_RiskService_Sdk_IConfig
         * @return Radial_RiskService_Sdk_IApi
         * @codeCoverageIgnore
         */
        protected function _getApi(Radial_RiskService_Sdk_IConfig $config)
        {
                return $this->_getNewSdkInstance('Radial_RiskService_Sdk_Api', $config);
        }

	 /**
        * Get new empty request payload
        *
        * @return Radial_RiskService_Sdk_IPayload
        */
        protected function _getNewOCREmptyRequest()
        {
                return $this->_getNewSdkInstance('Radial_RiskService_Sdk_OrderConfirmationRequest');
        }

	 /**
        * Get new empty request payload
        *
        * @return Radial_RiskService_Sdk_IPayload
        */
        protected function _getNewOCREmptyResponse()
        {
                return $this->_getNewSdkInstance('Radial_RiskService_Sdk_OCResponse');
        }

	/**
         * @param  Radial_RiskService_Sdk_IApi
         * @return Radial_RiskService_Sdk_IPayload | null
         */
        protected function _sendRequest(Radial_RiskService_Sdk_IApi $api, Mage_Sales_Model_Order $order, $payload = null, $retry = null )
        {
                $response = null;

                try {
                        $api->send();

			if( $order->getId() )
			{
				// Set order state / status below, then use order history collection
        			$order->setState("pending", "risk_submitted", 'Order has been submitted for Fraud Review.', false);
        			$order->save();
			}

                        $response = $api->getResponseBody();
                } catch (Exception $e) {
                        $logMessage = sprintf('[%s] The following error has occurred while sending request: %s', __CLASS__, $e->getMessage());
                        Mage::log($logMessage, Zend_Log::WARN);
                        Mage::logException($e);

			$fraudEmailA = explode(',', $this->_config->getFraudEmail());
 
			if( !empty($fraudEmailA) )
			{
				foreach( $fraudEmailA as $fraudEmail )
				{
					$fraudName = Mage::app()->getStore()->getName() . ' - ' . 'Fraud Admin';

					$emailTemplate  = Mage::getModel('core/email_template')->loadDefault('custom_email_template1');

					//Create an array of variables to assign to template
					$emailTemplateVariables = array();
					$emailTemplateVariables['myvar1'] = gmdate("Y-m-d\TH:i:s\Z");
					$emailTemplateVariables['myvar2'] = $e->getMessage();
					$emailTemplateVariables['myvar3'] = $e->getTraceAsString();
					$emailTemplateVariables['myvar4'] = htmlspecialchars($payload);
					$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			
					//Sending E-Mail to Fraud Admin Email.
                                	$mail = Mage::getModel('core/email')
                                                ->setToName($fraudName)
                                                ->setToEmail($fraudEmail)
                                                ->setBody($processedTemplate)
                                                ->setSubject('Fraud Exception Report From: '. __CLASS__ . ' on ' . gmdate("Y-m-d\TH:i:s\Z") . ' UTC')
                                                ->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email'))
                                                ->setFromName($fraudName)
                                                ->setType('html');
                                	try{
                                	   //Confimation E-Mail Send
                                	   $mail->send();
                                	}
                                	catch(Exception $error)
                                	{
                                		$logMessage = sprintf('[%s] Error Sending Email: %s', __CLASS__, $error->getMessage());
                                        	Mage::log($logMessage, Zend_Log::ERR);
                                	}
				}
			}

			if( !$retry )
			{
				$xml = simplexml_load_string($payload);
				if( strcmp( $xml->getName(), "RiskAssessmentRequest") === 0)
				{
					$eventName = 'risk_assessment_request';
				} elseif (  strcmp( $xml->getName(), "RiskOrderConfirmationRequest") === 0) {
					$eventName = 'order_confirmation_request';
				} else {
					$eventName = 'not_supported';
				}

				$object = Mage::getModel('radial_core/retryQueue');
	        		$time = time();
	        		$data = array('event_name' => $eventName, 'created_at' => $time, 'message_content' => $payload, 'delivery_status' => 0);
	        		$object->setData($data);
	        		$object->save();
                	} 
	
			if( $order->getId() )
			{
				$order->setState("pending", "risk_retrysubmit", 'Order Has NOT been transmitted to Fraud... Retrying... Contact Radial Fraud Team if the order continues to be in this state.', false);
                                $order->save();
			}
        	}

		return $response;
	}

    /**
     * Get new empty request payload
     *
     * @return Radial_RiskService_Sdk_IPayload
     */
    protected function _getNewEmptyRequest()
    {
        return $this->_getNewSdkInstance('Radial_RiskService_Sdk_Request');
    }

	/**
	 * Get new empty response payload
	 *
	 * @return Radial_RiskInsight_Sdk_IPayload
	 */
	protected function _getNewEmptyResponse()
	{
		return $this->_getNewSdkInstance('Radial_RiskService_Sdk_Response');
	}

	public function processRiskOrder(
		Mage_Sales_Model_Order $order, Varien_Event_Observer $observer
	)
	{
        $request = $this->_getNewEmptyRequest();

	try
	{
        	$payload = Mage::getModel('radial_eb2cfraud/build_request', array(
        	    'request' => $request,
        	    'order' => $order,
        	))->build();

		$this->_payloadXml = $payload->serialize();

               $object = Mage::getModel('radial_core/retryQueue');
               $time = time();
               $data = array('event_name' => 'risk_assessment_request', 'created_at' => $time, 'message_content' => $this->_payloadXml, 'delivery_status' => 0);
               $object->setData($data);
               $object->save();

		// Set order state / status below, then use order history collection
        	$order->setState("processing", "risk_processing", 'Order is now processing in the Fraud System.', false);
        	$order->save();
	} catch( Exception $e ) {
                $logMessage = sprintf('[%s] Error Payload RiskAssessmentRequest Body: %s', __CLASS__, $e->getMessage());
                Mage::log($logMessage, Zend_Log::WARN);
        }
	}

        public function processOrderConfirmationRequest(Varien_Event_Observer $observer)
        {
                $order = $observer->getOrder();
		$orderNull = Mage::getModel('sales/order');
		$orderBefore = Mage::getModel('sales/order')->load($order->getId());

                $request = $this->_getNewOCREmptyRequest();

		if( $order->getState() != Mage_Sales_Model_Order::STATE_NEW && $order->getState() != 'pending' && $orderBefore->getState() != Mage_Sales_Model_Order::STATE_CANCELED )
		{
			$allPending = true;
			$collectionReturnSize = Mage::getResourceModel('sales/order_creditmemo_collection')
						  ->addAttributeToFilter('increment_id', $order->getOrderId())->getSize();
	

			if($order->hasInvoices())
			{
				foreach ($order->getInvoiceCollection() as $inv) {
    					if( $inv->getState() !== Mage_Sales_Model_Order_Invoice::STATE_OPEN )
					{
						$allPending = false;
						break;
					}
				}
			}
 
			if( !$allPending || $order->hasShipments() || $collectionReturnSize > 0 || $order->getState() === Mage_Sales_Model_Order::STATE_COMPLETE || $order->getState() === Mage_Sales_Model_Order::STATE_CLOSED || $order->getState() === Mage_Sales_Model_Order::STATE_CANCELED || strcmp($orderBefore->getState(), 'ready_ship') === 0)
			{
				try
				{
                			$payload = Mage::getModel('radial_eb2cfraud/build_OCRequest', array(
                				'request' => $request,
                			        'order' => $order,
                			))->build();
					$this->_payloadXml = $payload->serialize();

					$object = Mage::getModel('radial_core/retryQueue');
               				$time = time();
               				$data = array('event_name' => 'order_confirmation_request', 'created_at' => $time, 'message_content' => $this->_payloadXml, 'delivery_status' => 0);
               				$object->setData($data);
               				$object->save();
				} catch( Exception $e ) {
					$logMessage = sprintf('[%s] Error Payload OrderConfirmationRequest Body: %s', __CLASS__, $e->getMessage());
                	        	Mage::log($logMessage, Zend_Log::WARN);
				}	
			}
		}	
        }


    /**
     * Sends the event from the database to Sonic, as configured in local.xml
     */
    public function sendEvent() {
        $objectCollection = Mage::getModel('radial_core/retryQueue')->getCollection()->setPageSize(100);
	$order = Mage::getModel('sales/order');

	$pages = $objectCollection->getLastPageNumber();
        $currentPage = 1;

        do
        {
		$objectCollection->setCurPage($currentPage);
                $objectCollection->load();

        	foreach( $objectCollection as $object )
        	{
			$this->payloadXml = null;
                	$apiConfig = null;
                	$this->_request = $this->_getNewSdkInstance('Radial_RiskService_Sdk_Request');
                	$this->_OCrequest = $this->_getNewSdkInstance('Radial_RiskService_Sdk_OrderConfirmationRequest');

			try
			{
				if ( $object->getDeliveryStatus() < $this->_config->getMaxRetries())
				{
					if( strcmp($object->getEventName(), "order_confirmation_request") === 0)
                			{
                        			$this->_payloadXml = $this->_OCrequest->deserialize($object->getMessageContent());
                        			$apiConfig = $this->_setupApiConfig($this->_payloadXml, $this->_getNewOCREmptyResponse());
                			} else {
                        			$this->_payloadXml = $this->_request->deserialize($object->getMessageContent());
                        			$apiConfig = $this->_setupApiConfig($this->_payloadXml, $this->_getNewEmptyResponse());
                			}

        				$response = $this->_sendRequest($this->_getApi($apiConfig), $order, $object->getMessageContent(), 1 );
					if( $response )
					{
						$object->delete();
					} else {
						//Queue up for retry
                        			if (strlen($object->getMessageContent()) > 0) {
                                			if( $object->getDeliveryStatus() < $this->_config->getMaxRetries())
                                			{
                                        			$previousStatus = $object->getDeliveryStatus();
                                        			$object->setDeliveryStatus($previousStatus+1);
                                        			$object->save();
                                			} else {
                                         			$logMessage = sprintf('[%s] Error Transmitting Message (MAX RETRIES) - Body: %s', __CLASS__, $e->getMessage());
        	                                 		Mage::log($logMessage, Zend_Log::ERR);
	
                	                			$fraudEmailA = explode(',', $this->_config->getFraudEmail());

                        					if( !empty($fraudEmailA) )
                        					{
                                					foreach( $fraudEmailA as $fraudEmail )
                                					{
										$fraudName = Mage::app()->getStore()->getName() . ' - ' . 'Fraud Admin';

                                						$emailTemplate  = Mage::getModel('core/email_template')->loadDefault('custom_email_template1');
	
        	                        					//Create an array of variables to assign to template
                	                					$emailTemplateVariables = array();
                        	        					$emailTemplateVariables['myvar1'] = gmdate("Y-m-d\TH:i:s\Z");
                                						$emailTemplateVariables['myvar2'] = $e->getMessage();
										$emailTemplateVariables['myvar3'] = $e->getTraceAsString();
										$emailTemplateVariables['myvar4'] = htmlspecialchars($object->getMessageContent());

                                						$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
	
										//Sending E-Mail to Fraud Admin Email.
										$mail = Mage::getModel('core/email')
 											->setToName($fraudName)
 											->setToEmail($fraudEmail)
 											->setBody($processedTemplate)
 											->setSubject('Fraud Exception Report From: '. __CLASS__ . ' on ' . gmdate("Y-m-d\TH:i:s\Z") . ' UTC')
 											->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email'))
 											->setFromName($fraudName)
 											->setType('html');
 										try{
 											//Confimation E-Mail Send
 											$mail->send();
 										}
 										catch(Exception $error)
 										{
 											$logMessage = sprintf('[%s] Error Sending Email: %s', __CLASS__, $error->getMessage());
                         								Mage::log($logMessage, Zend_Log::ERR);
										}
                        						}
								}

                                			}
                        			}
					}
				}
			} catch( Exception $e ) {
			 	$logMessage = sprintf('[%s] Error JOB Retransmission: %s', __CLASS__, $e->getMessage());
                         	Mage::log($logMessage, Zend_Log::ERR);

                         	$fraudEmailA = explode(',', $this->_config->getFraudEmail());

                         	if( !empty($fraudEmailA) )
                         	{
                                	foreach( $fraudEmailA as $fraudEmail )
                                	{
						$fraudName = Mage::app()->getStore()->getName() . ' - ' . 'Fraud Admin';

                                		$emailTemplate  = Mage::getModel('core/email_template')->loadDefault('custom_email_template1');

                                		//Create an array of variables to assign to template
                                		$emailTemplateVariables = array();
                                		$emailTemplateVariables['myvar1'] = gmdate("Y-m-d\TH:i:s\Z");
                                		$emailTemplateVariables['myvar2'] = $e->getMessage();
						$emailTemplateVariables['myvar3'] = $e->getTraceAsString();
						$emailTemplateVariables['myvar4'] = htmlspecialchars($object->getMessageContent());

                                		$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                        			//Sending E-Mail to Fraud Admin Email.
                                		$mail = Mage::getModel('core/email')
                                                     ->setToName($fraudName)
                                                     ->setToEmail($fraudEmail)
                                                     ->setBody($processedTemplate)
                                                     ->setSubject('Fraud Exception Report From: '. __CLASS__ . ' on ' . gmdate("Y-m-d\TH:i:s\Z") . ' UTC')
                                                     ->setFromEmail(Mage::getStoreConfig('trans_email/ident_general/email'))
                                                     ->setFromName($fraudName)
                                                     ->setType('html');
                                		try {  
                                	   		//Confimation E-Mail Send
                                	   		$mail->send();
                                		}
                                		catch(Exception $error)
                                		{
                                			$logMessage = sprintf('[%s] Error Sending Email: %s', __CLASS__, $error->getMessage());
                                        		Mage::log($logMessage, Zend_Log::ERR);
                                		}
					}
				}
			}
        	}

		$currentPage++;
                $objectCollection->clear();
        } while ($currentPage <= $pages);
    }
}
