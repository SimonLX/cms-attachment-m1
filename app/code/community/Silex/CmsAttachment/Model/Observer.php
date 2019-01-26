<?php

class Silex_CmsAttachment_Model_Observer
{
    /**
     * Delete saved PDF files, to force a new generation
     *
     * @param Varien_Event $observer observer
     */
    public function deleteSavedPdfFile($observer)
    {
        /** @var Mage_Cms_Model_Page $cmsPage */
        $cmsPage = $observer->getEvent()->getObject();

        if ($cmsPage->getId()) {
            $fileExists = Mage::helper('silex_cmsattachment/pdf')->checkCmsPagePdf($cmsPage->getId());

            if ($fileExists) {
                $path = Mage::helper('silex_cmsattachment/pdf')->getPathToPdf($cmsPage->getId());
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
     * Add configured CMS pages PDF to mail before send
     *
     * @param Varien_Event $observer observer
     */
    public function addPdfCmsPagesToMail($observer)
    {
        /** @var Mage_Core_Model_Email_Template_Mailer $mailer */
        $mailer = $observer->getEvent()->getMailer();
        $type = $observer->getEvent()->getType();

        if (!empty($type)) {
            $cmsPagesToAdd = Mage::getStoreConfig('sales_email/'. $type .'/cms_pages_attached');

            if (!empty($cmsPagesToAdd)) {
                $cmsPagesCode = explode(',', $cmsPagesToAdd);
                /** @var Silex_CmsAttachment_Helper_Pdf $helper */
                $helper = Mage::helper('ccl_cms/pdf');

                foreach ($cmsPagesCode as $code) {
                    $cmsPagePdf = $helper->getCmsPagePdf($code, true);

                    if ($cmsPagePdf) {
                        $mailer->addAttachment($cmsPagePdf, $code . '.pdf');
                    }
                }
            }
        }
    }
} 