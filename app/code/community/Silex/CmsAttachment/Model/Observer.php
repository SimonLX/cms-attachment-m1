<?php

/**
 * Class Silex_CmsAttachment_Model_Observer
 *
 * Default observer
 */
class Silex_CmsAttachment_Model_Observer
{
    /**
     * Delete saved PDF files, to force a new generation
     *
     * @param Varien_Event $observer
     *
     * @return void
     */
    public function deleteSavedPdfFile($observer)
    {
        /** @var Mage_Cms_Model_Page $cmsPage */
        $cmsPage = $observer->getEvent()->getObject();

        if ($cmsPage->getId()) {
            /** @var Silex_CmsAttachment_Helper_Pdf $helper */
            $helper = Mage::helper('silex_cmsattachment/pdf');

            if ($helper->checkCmsPagePdf($cmsPage->getId())) {
                $path = $helper->getPathToPdf($cmsPage->getId());

                try {
                    unlink($path);
                } catch (Exception $e) {
                    Mage::logException($e);
                    Mage::getModel('adminhtml/session')
                        ->addError(
                            'Unable to delete PDF file of CMS page #' . $cmsPage->getId()
                            . '. Exception : ' . $e->getMessage()
                        );
                }
            }
        }
    }

    /**
     * Add configured CMS pages PDF to transactional mail before send
     *
     * @param Varien_Event $observer
     *
     * @return void
     */
    public function addPdfCmsPagesToTransactionalMail($observer)
    {
        /** @var Mage_Core_Model_Email_Queue $message */
        $message = $observer->getEvent()->getMessage();
        /** @var Zend_Mail $mailer */
        $mailer = $observer->getEvent()->getMailer();

        if (!empty($message)) {
            $eventType = $message->getEventType();
            $configPathPart = $this->_getConfigPathPart($eventType);

            if ($configPathPart) {
                $cmsPagesToAdd = Mage::getStoreConfig('sales_email/' . $configPathPart . '/cms_pages_attached');

                if (!empty($cmsPagesToAdd)) {
                    $cmsPagesCode = explode(',', $cmsPagesToAdd);
                    /** @var Silex_CmsAttachment_Helper_Pdf $helper */
                    $helper = Mage::helper('silex_cmsattachment/pdf');

                    foreach ($cmsPagesCode as $code) {
                        $cmsPagePdf = $helper->getCmsPagePdf($code, true);

                        if ($cmsPagePdf) {
                            $mailer->createAttachment(
                                $cmsPagePdf,
                                'application/pdf',
                                Zend_Mime::DISPOSITION_ATTACHMENT,
                                Zend_Mime::ENCODING_BASE64,
                                $code . '.pdf'
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Get correct config path part from event type
     *
     * @param string $eventType
     *
     * @return string|false
     */
    protected function _getConfigPathPart($eventType)
    {
        $matchingEvent = array(
            Mage_Sales_Model_Order::EMAIL_EVENT_NAME_NEW_ORDER                  => 'order',
            Mage_Sales_Model_Order::EMAIL_EVENT_NAME_UPDATE_ORDER               => 'order_comment',
            Silex_EmailExtensions_Model_Constants::EVENT_TYPE_INVOICE_NEW       => 'invoice',
            Silex_EmailExtensions_Model_Constants::EVENT_TYPE_INVOICE_UPDATE    => 'invoice_comment',
            Silex_EmailExtensions_Model_Constants::EVENT_TYPE_SHIPMENT_NEW      => 'shipment',
            Silex_EmailExtensions_Model_Constants::EVENT_TYPE_SHIPMENT_UPDATE   => 'shipment_comment',
            Silex_EmailExtensions_Model_Constants::EVENT_TYPE_CREDITMEMO_NEW    => 'creditmemo',
            Silex_EmailExtensions_Model_Constants::EVENT_TYPE_CREDITMEMO_UPDATE => 'creditmemo_comment'
        );

        return array_key_exists($eventType, $matchingEvent) ? $matchingEvent[$eventType] : false;
    }
} 