<?php

require_once 'Mage/Cms/controllers/PageController.php';

/**
 * Class Silex_CmsAttachment_PageController
 */
class Silex_CmsAttachment_PageController extends Mage_Cms_PageController
{
    public function printAction()
    {
        $pageId = $this->getRequest()->getParam('page_id', $this->getRequest()->getParam('id', false));

        /** @var Mage_Cms_Model_Page $page */
        $page = Mage::getSingleton('cms/page')->load($pageId);
        /** @var Silex_CmsAttachment_Helper_Pdf $pdfHelper */
        $pdfHelper = Mage::helper('silex_cmsattachment/pdf');

        if ($page->getId()) {
            // Check if the PDF is already generated
            $pdfExists = $pdfHelper->checkCmsPagePdf($page->getId());

            if (!$pdfExists) {
                // Save PDF in media
                $pdfHelper->saveCmsPagePdf($page->getId());
            }

            $pdf = $pdfHelper->getCmsPagePdf($page->getId());

            // Send correct header
            $response = $this->getResponse();
            $response->setHeader('HTTP/1.1 200 OK','')
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Content-Disposition', 'attachment; filename='. $page->getIdentifier() . '.pdf')
                ->setHeader('Last-Modified', date('r'))
                ->setHeader('Accept-Ranges', 'bytes')
                ->setHeader('Content-Length', strlen($pdf))
                ->setHeader('Content-type', 'application/pdf');
            $response->setBody($pdf);
            $response->sendResponse();
        } else {
            // Send 404
            $response = $this->getResponse();
            $response->setHeader('HTTP/1.1', '404 Not Found')
                ->setHeader('Status', '404 File not found');

            $this->_redirect('cms/index/noRouteAction');
        }
    }
}
