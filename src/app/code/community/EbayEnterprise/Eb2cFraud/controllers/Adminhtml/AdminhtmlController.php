<?php
 
class EbayEnterprise_Eb2cFraud_Adminhtml_AdminhtmlController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
	//Load Layout
	$this->loadLayout();

        //Render Layout
        $this->renderLayout();
    }

    /**
     * Reset Messages at Maximum Retries
     */ 
    public function messageResetAction()
    {
        Mage::getSingleton('adminhtml/session')->addSuccess("Successfully Reset Messages at Maximum Transmission");

	$objectCollection = Mage::getModel('ebayenterprise_eb2cfraud/retryQueue')->getCollection()->setPageSize(100)->addFieldToFilter('delivery_status', 2);
        $pages = $objectCollection->getLastPageNumber();
        $currentPage = 1;

        do
        {
                $objectCollection->setCurPage($currentPage);
                $objectCollection->load();

                foreach($objectCollection as $object)
                {
                        $object->setDeliveryStatus(0);
                        $object->save();
                }

                $currentPage++;
                $objectCollection->clear();
        } while ($currentPage <= $pages);

        $this->_redirect('adminhtml/system_config/edit/section/radial_core');
    }

    /**
     * Purge all messages in the retry queue
     */ 
    public function purgeRetryQueueAction()
    {
        Mage::getSingleton('adminhtml/session')->addSuccess("Successfully Purged Retry Messages Queue");

	$objectCollection = Mage::getModel('ebayenterprise_eb2cfraud/retryQueue')->getCollection()->setPageSize(100);
        $pages = $objectCollection->getLastPageNumber();
        $currentPage = 1;

        do
        {
                $objectCollection->setCurPage($currentPage);
                $objectCollection->load();

                foreach($objectCollection as $object)
                {
                        $object->delete();
                }

                $currentPage++;
                $objectCollection->clear();
        } while ($currentPage <= $pages);

        $this->_redirect('adminhtml/system_config/edit/section/radial_core');
    }
}