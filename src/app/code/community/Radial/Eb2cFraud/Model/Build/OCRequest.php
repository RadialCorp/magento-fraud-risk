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

class Radial_Eb2cFraud_Model_Build_OCRequest
    extends Radial_Eb2cFraud_Model_Abstract
    implements Radial_Eb2cFraud_Model_Build_IOCRequest
{
    /** @var Radial_RiskService_Sdk_IPayload */
    protected $_request;
    /** @var Radial_Eb2cFraud_Model_Risk_Service */
    protected $_service;
    /** @var Mage_Sales_Model_Order */
    protected $_order;
    /** @var Mage_Sales_Model_Quote */
    protected $_quote;
    /** @var Radial_Eb2cFraud_Helper_Data */
    protected $_helper;
    /** @var Radial_Eb2cFraud_Helper_Http */
    protected $_httpHelper;
    /** @var Radial_Eb2cFraud_Helper_Config */
    protected $_config;
    /** @var Mage_Catalog_Model_Product */
    protected $_product;
    /** @var Radial_Core_Helper_Shipping */
    protected $_shippingHelper;

    /**
     * @param array $initParams Must have this key:
     *                          - 'request' => Radial_RiskService_Sdk_IPayload
     *                          - 'order' => Mage_Sales_Model_Order
     *                          - 'quote' => Mage_Sales_Model_Quote
     *                          - 'helper' => Radial_Eb2cFraud_Helper_Data
     *                          - 'product' => Mage_Catalog_Model_Product
     *				- 'config'  => Radial_Eb2cFraud_Helper_config
     *				- 'shippinghelper' => Radial_Core_Helper_Shipping
     */
    public function __construct(array $initParams=array())
    {
        list($this->_request, $this->_order, $this->_quote, $this->_helper, $this->_httpHelper, $this->_product, $this->_config, $this->_service, $this->_shippingHelper) = $this->_checkTypes(
            $this->_nullCoalesce($initParams, 'request', $this->_getNewSdkInstance('Radial_RiskService_Sdk_OrderConfirmationRequest')),
            $this->_nullCoalesce($initParams, 'order', $initParams['order']),
            $this->_nullCoalesce($initParams, 'quote', Mage::getModel('sales/quote')),
            $this->_nullCoalesce($initParams, 'helper', Mage::helper('radial_eb2cfraud')),
            $this->_nullCoalesce($initParams, 'http_helper', Mage::helper('radial_eb2cfraud/http')),
            $this->_nullCoalesce($initParams, 'product', Mage::getModel('catalog/product')),
	    $this->_nullCoalesce($initParams, 'config', Mage::helper('radial_eb2cfraud/config')),
	    $this->_nullCoalesce($initParams, 'service', Mage::getModel('radial_eb2cfraud/risk_service')),
	    $this->_nullCoalesce($initParams, 'shipping_helper', Mage::helper('radial_core/shipping'))
        );
    }

    /**
     * Type hinting for self::__construct $initParams
     *
     * @param  Radial_RiskService_Sdk_IPayload
     * @param  Mage_Sales_Model_Order
     * @param  Mage_Sales_Model_Quote
     * @param  Radial_Eb2cFraud_Helper_Data
     * @param  Radial_Eb2cFraud_Helper_Http
     * @param  Mage_Catalog_Model_Product
     * @param  Radial_Eb2cFraud_Helper_Config
     * @param  Radial_Eb2cFraud_Model_Risk_Service
     * @param  Radial_Core_Helper_Shipping
     * @return array
     */
    protected function _checkTypes(
        Radial_RiskService_Sdk_IPayload $request,
        Mage_Sales_Model_Order $order,
        Mage_Sales_Model_Quote $quote,
        Radial_Eb2cFraud_Helper_Data $helper,
        Radial_Eb2cFraud_Helper_Http $httpHelper,
        Mage_Catalog_Model_Product $product,
	Radial_Eb2cFraud_Helper_Config $config,
	Radial_Eb2cFraud_Model_Risk_Service $service,
	Radial_Core_Helper_Shipping $shippingHelper
    ) {
        return array($request, $order, $quote, $helper, $httpHelper, $product, $config, $service, $shippingHelper);
    }

    public function build()
    {
        $this->_buildRequest();
        return $this->_request;
    }

    /**
     * @return self
     */
    protected function _buildRequest()
    {
        $this->_buildOrder($this->_request->getOrder());
        return $this;
    }

    /**
     * @param  Radial_RiskService_Sdk_IOCROrder
     * @return self
     */
    protected function _buildOrder(Radial_RiskService_Sdk_IOCROrder $subPayloadOrder)
    {
        $subPayloadOrder->setOrderId($this->_order->getIncrementId());
	$subPayloadOrder->setStoreId($this->_config->getStoreId());
	
	$statusDate =  new \DateTime(null, new \DateTimeZone("UTC"));
	$subPayloadOrder->setStatusDate($statusDate);

	//Verify if there are any canceled or returned items this changes order status / confirmation status
	$canceledItems = 0;
	$returnedItems = 0;
	$refundedItems = 0;
	$invoicedItems = 0;
	$shippedItems = 0;
	$orderedItems = 0;

	foreach ($this->_order->getAllItems() as $orderItem) {
            if( $orderItem->getProductType() === Mage_Catalog_Model_Product_Type::TYPE_SIMPLE || $orderItem->getProductType() === Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL )
            {
		if( $orderItem->getParentItemId() )
		{
			$orderItemP = $this->_order->getItemsCollection()
    						->addAttributeToSelect('*')
    						->addAttributeToFilter('item_id', array('eq'=>$orderItem->getParentItemId()))
    						->getFirstItem();

			$qtyCanceled = $orderItemP->getQtyCanceled();
                        $qtyReturned = $orderItemP->getQtyReturned();
                        $qtyRefunded = $orderItemP->getQtyRefunded();
                        $qtyInvoiced = $orderItemP->getQtyInvoiced();
                        $qtyShipped = $orderItemP->getQtyShipped();
                        $qtyOrdered = $orderItemP->getQtyOrdered();
		} else {
			$qtyCanceled = $orderItem->getQtyCanceled();
                	$qtyReturned = $orderItem->getQtyReturned();
			$qtyRefunded = $orderItem->getQtyRefunded();
			$qtyInvoiced = $orderItem->getQtyInvoiced();
			$qtyShipped = $orderItem->getQtyShipped();
			$qtyOrdered = $orderItem->getQtyOrdered();
		}

		$shippedItems += $qtyShipped;
		$orderedItems += $qtyOrdered;
		$canceledItems += $qtyCanceled;
		$returnedItems += $qtyReturned;
		$refundedItems += $qtyRefunded;
		$invoicedItems += $qtyInvoiced;
	     }
	}
			
	if( $canceledItems && !$returnedItems && !$refundedItems)
	{
		$subPayloadOrder->setConfirmationType("CANCEL");
		
		//Find out how many invoiced only items are left.
		$diff = (int)$invoicedItems -(int)$shippedItems;
		if($diff)
		{
			$subPayloadOrder->setOrderStatus("IN_PROCESS");
		} else {
			$subPayloadOrder->setOrderStatus("COMPLETED");
		}
	} elseif ($canceledItems && $returnedItems && !$refundedItems) {
		$subPayloadOrder->setConfirmationType("RETURN PROCESSED");
                $subPayloadOrder->setOrderStatus("COMPLETED");
	} elseif ($canceledItems && $refundedItems) {
                $subPayloadOrder->setConfirmationType("CREDIT ISSUED");
                $subPayloadOrder->setOrderStatus("COMPLETED");
	} elseif ($shippedItems === $orderedItems ) {
		if( $refundedItems )
		{
			$subPayloadOrder->setConfirmationType("CREDIT ISSUED");
		} else {
			$subPayloadOrder->setConfirmationType("SHIPMENT");
		}
                $subPayloadOrder->setOrderStatus("COMPLETED");
	} elseif ($shippedItems !== $orderedItems ) {
                if( $refundedItems )
                {
                        $subPayloadOrder->setConfirmationType("CREDIT ISSUED");
                } else {
                        $subPayloadOrder->setConfirmationType("SHIPMENT");
                }
		$subPayloadOrder->setOrderStatus("IN_PROCESS");
	} else {
		$subPayloadOrder->setConfirmationType($this->_config->getOrderStateForConfirmationFraudOCR($this->_order->getState()));
        	$subPayloadOrder->setOrderStatus($this->_config->getOrderStateForFraudOCR($this->_order->getState()));
	}


	$this->_buildLineDetails($subPayloadOrder->getLineDetails());

	$collectionReturnSize = Mage::getResourceModel('sales/order_creditmemo_collection')
                                                  ->addAttributeToFilter('order_id', $this->_order->getId())->getSize();

	if( $collectionReturnSize > 0 || $this->_order->getState() === Mage_Sales_Model_Order::STATE_CLOSED )
	{
		$this->_buildCustomAttributesList($subPayloadOrder->getCustomAttributesList());
	}

        return $this;
    }

    /**
     * @param  Radial_RiskService_Sdk_CustomAttributes_IList
     * @return self
     */
    public function _buildCustomAttributesList(Radial_RiskService_Sdk_CustomAttributes_IList $subPayloadCustomAttributesList )
    {
	$subPayloadCustomAttribute = $subPayloadCustomAttributesList->getEmptyCustomAttribute();
	$this->_buildCustomAttribute($subPayloadCustomAttribute, "RefundedAmount");
	$subPayloadCustomAttributesList->offsetSet($subPayloadCustomAttribute);
    }

    /**
     * @param  Radial_RiskService_Sdk_ICustomAttribute
     * @return self
     */
    public function _buildCustomAttribute(Radial_RiskService_Sdk_ICustomAttribute $subPayloadCustomAttribute, $attributeName )
    {
	if( strcmp($attributeName, "RefundedAmount") === 0 )
	{
		$collectionReturn = Mage::getResourceModel('sales/order_creditmemo_collection')
                                                  ->addAttributeToFilter('order_id', $this->_order->getId());
		$collectionReturnSize = $collectionReturn->getSize();

		if( $collectionReturnSize > 0)
        	{
			$subPayloadCustomAttribute->setAttributeName($attributeName);
			$grandTotal = 0;

			foreach( $collectionReturn as $creditMemo ) 
                	{
                        	$grandTotal += $creditMemo->getGrandTotal();
                	}

			$grandTotal = sprintf('%01.2F', $grandTotal);
			$subPayloadCustomAttribute->setAttributeValue($grandTotal);	
		} else {
			//Assume the full amount was just refunded
			$subPayloadCustomAttribute->setAttributeName($attributeName);
                        $grandTotal = sprintf('%01.2F', $this->_order->getGrandTotal());
                        $subPayloadCustomAttribute->setAttributeValue($grandTotal);
		}
	}
    }

    /**
     * @param  Radial_RiskService_Sdk_Line_IDetails
     * @return self
     */
    protected function _buildLineDetails(Radial_RiskService_Sdk_Line_IDetails $subPayloadLineDetails)
    {
        foreach ($this->_order->getAllItems() as $orderItem) {
	    if( $orderItem->getProductType() === Mage_Catalog_Model_Product_Type::TYPE_SIMPLE || $orderItem->getProductType() === Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL )
	    {
	    	$quaduple = array();

		if( $orderItem->getParentItemId() )
		{
			$orderItemP = $this->_order->getItemsCollection()
                                                ->addAttributeToSelect('*')
                                                ->addAttributeToFilter('item_id', array('eq'=>$orderItem->getParentItemId()))
                                                ->getFirstItem();

                        $qtyCanceled = $orderItemP->getQtyCanceled();
                        $qtyReturned = $orderItemP->getQtyReturned();
                        $qtyRefunded = $orderItemP->getQtyRefunded();
                        $qtyInvoiced = $orderItemP->getQtyInvoiced();
                        $qtyShipped = $orderItemP->getQtyShipped();
                        $qtyOrdered = $orderItemP->getQtyOrdered();
		} else {
			$qtyShipped = $orderItem->getQtyShipped();
                	$qtyOrdered = $orderItem->getQtyOrdered();
                	$qtyCanceled = $orderItem->getQtyCanceled();
                	$qtyReturned = $orderItem->getQtyReturned();
                	$qtyRefunded = $orderItem->getQtyRefunded();
		}

	    	foreach($this->_order->getShipmentsCollection() as $shipment){
            		foreach ($shipment->getAllItems() as $product){
               		    if( strcmp($product->getSku(),$orderItem->getSku()) === 0)
            		    {
                	        //This product is on this shipment, so record, NOTE Magento does not assign items tracking numbers but shipments, shipments may have multiple tracking numbers
                	        foreach($shipment->getAllTracks() as $tracking_number){
                        	        $track_num = $tracking_number->getNumber();
                        	        $carrier_code = $tracking_number->getCarrierCode();
                        	        $delivery_method = $this->_shippingHelper->getMethodSdkId($this->_order->getShippingMethod());
					$shipacount = $this->_helper->getNewDateTime($tracking_number->getCreatedAt());

					$quaduple[] = array( 'tracking_number' => $track_num, 'carrier_code' => $carrier_code, 'delivery_method' => $delivery_method, 'shipacount' => $shipacount);
				}
		     	     }
			}
	    	}

		if( (int)$qtyRefunded === 0)
                {
	    		foreach( $quaduple as $quad )
	    		{
	    			$subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
            			$this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quad, $qtyShipped, 1);
            			$subPayloadLineDetails->offsetSet($subPayloadLineDetail);
            		}
		}

	    	if( empty($quaduple))
	    	{
			if( (int)$qtyShipped !== 0 )
			{
				$quaduple = array( 'tracking_number' => '', 'carrier_code' => '', 'delivery_method' => '', 'shipacount' => '');
				$diff = (int)$qtyOrdered - (int)$qtyShipped - (int)$qtyCanceled - (int)$qtyReturned - (int)$qtyRefunded;

				if( (int)$diff > 0 )
				{
					$subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
                			$this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quaduple, $diff, 0);
                			$subPayloadLineDetails->offsetSet($subPayloadLineDetail);
				}

				if( (int)$qtyCanceled !== 0 )
                                {
                                	$subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
                                        $this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quaduple, $qtyCanceled, 2);
                                        $subPayloadLineDetails->offsetSet($subPayloadLineDetail);
                                }

                                if( (int)$qtyReturned !== 0 )
                                {
                                	$subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
                                        $this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quaduple, $qtyReturned, 3);
                                        $subPayloadLineDetails->offsetSet($subPayloadLineDetail);
                                }

				if( (int)$qtyRefunded !== 0 )
                                {
                                	$subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
                                        $this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quaduple, $qtyRefunded, 4);
                                        $subPayloadLineDetails->offsetSet($subPayloadLineDetail);
                                } else {
					$subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
                                        $this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quaduple, $qtyShipped, 1);
                                        $subPayloadLineDetails->offsetSet($subPayloadLineDetail);
				}
			} else {
				$quaduple = array( 'tracking_number' => '', 'carrier_code' => '', 'delivery_method' => '', 'shipacount' => '');
				$trueDiff = (int)$qtyOrdered - (int)$qtyReturned - (int)$qtyCanceled - (int)$qtyRefunded;

				if( $trueDiff > 0 )
				{
					$subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
                                	$this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quaduple, $trueDiff, 0);
                                	$subPayloadLineDetails->offsetSet($subPayloadLineDetail);
				}

				if( (int)$qtyReturned !== 0 )
				{
					$subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
                        		$this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quaduple, $qtyReturned, 3);
                        		$subPayloadLineDetails->offsetSet($subPayloadLineDetail);
				}

				if( (int)$qtyCanceled !== 0 )
                        	{
                                	$subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
                                	$this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quaduple, $qtyCanceled, 2);
                                	$subPayloadLineDetails->offsetSet($subPayloadLineDetail);
                        	}

				if( (int)$qtyRefunded !== 0 )
                        	{
                                	$subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
                                	$this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quaduple, $qtyRefunded, 4);
                                	$subPayloadLineDetails->offsetSet($subPayloadLineDetail);
                        	}
			}
	    	} else {
		 	$quaduple = array( 'tracking_number' => '', 'carrier_code' => '', 'delivery_method' => '', 'shipacount' => '');
                 	$diff = (int)$qtyOrdered - (int)$qtyShipped - (int)$qtyCanceled - (int)$qtyReturned - (int)$qtyRefunded;

                 	if( (int)$diff > 0 )
                 	{
                 		$subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
                        	$this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quaduple, $diff, 0);
                        	$subPayloadLineDetails->offsetSet($subPayloadLineDetail);
                 	}

			if( (int)$qtyCanceled !== 0 )
                        {
                        	$subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
                                $this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quaduple, $qtyCanceled, 2);
                                $subPayloadLineDetails->offsetSet($subPayloadLineDetail);
                        }

                        if( (int)$qtyReturned !== 0 )
                        {
                        	$subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
                                $this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quaduple, $qtyReturned, 3);
                                $subPayloadLineDetails->offsetSet($subPayloadLineDetail);
                        }

			if( (int)$qtyRefunded !== 0 )
                        {
                                $subPayloadLineDetail = $subPayloadLineDetails->getEmptyLineDetail();
                                $this->_buildLineDetail($subPayloadLineDetail, $orderItem, $quaduple, $qtyRefunded, 4);
                                $subPayloadLineDetails->offsetSet($subPayloadLineDetail);
                        }
	    	}
	    }
	}
        return $this;
    }

    /**
     * @param  Radial_RiskService_Sdk_Line_IItem
     * @param  Mage_Core_Model_Abstract
     * @return self
     */
    protected function _buildLineDetail(
        Radial_RiskService_Sdk_Line_IDetail $subPayloadLineDetail,
        Mage_Core_Model_Abstract $orderItem, $quad, $qty, $status
    )
    {
	$subPayloadLineDetail->setSKU($orderItem->getSku());
	$subPayloadLineDetail->setQuantity((int) $qty);

	if( $status === 0 )
	{
		$subPayloadLineDetail->setItemStatus("PENDING");
	} elseif( $status === 1 ) {
		$subPayloadLineDetail->setItemStatus("SHIPPED");
	} elseif( $status === 2 ) {
		$subPayloadLineDetail->setItemStatus("CANCELLED");
	} elseif( $status === 3 ) {
		$subPayloadLineDetail->setItemStatus("RETURNED");
	} elseif( $status === 4 ) {
		$subPayloadLineDetail->setItemStatus("RETURNED");
	} else {
		$subPayloadLineDetail->setItemStatus($this->_config->getItemStateForFraudOCR($orderItem->getStatus()));
	}

	$subPayloadLineDetail->setTrackingNumber($quad['tracking_number']);
	
	if( $quad['carrier_code'] )
	{
		$subPayloadLineDetail->setShippingVendorCode($this->_config->getShipVendorForShipCarrier($quad['carrier_code']));
	}

	if( $orderItem->getIsVirtual() === 1)
	{
		$virtShipId = Mage::getStoreConfig('radial_core/shipping/virtual_shipping_method_id');
		$subPayloadLineDetail->setDeliveryMethod($virtShipId);
	} else {
		$subPayloadLineDetail->setDeliveryMethod($quad['delivery_method']);
	}	

	if( $quad['shipacount'] )
	{
		$subPayloadLineDetail->setShipScheduledDate($quad['shipacount']);
		$subPayloadLineDetail->setShipActualDate($quad['shipacount']);
	}

        return $this;
    }
}
