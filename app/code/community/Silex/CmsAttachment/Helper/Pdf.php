<?php

/**
 * Class Silex_CmsAttachment_Helper_Pdf
 */
class Silex_CmsAttachment_Helper_Pdf extends Mage_Core_Helper_Abstract
{
    protected $_path = '';

    public function __construct()
    {
        $this->_path = Mage::getBaseDir('media') . DS . 'cms_pages';
    }

    /**
     * Returns the path to PDF file for given page ID
     *
     * @param int|string $pageId ID/code of the page
     *
     * @return string Path of the file
     */
    public function getPathToPdf($pageId)
    {
        /** @var Mage_Cms_Model_Page $page */
        $page = Mage::getModel('cms/page')->load($pageId);

        $result = '';

        if ($page->getId()) {
            $result = $this->_path . DS . $page->getIdentifier() . '.pdf';
        }

        return $result;
    }

    /**
     * Check the PDF file saved in server for the CMS page
     *
     * @param int|string $pageId ID/code of the page
     *
     * @return false|mixed
     */
    public function checkCmsPagePdf($pageId)
    {
        $result = false;

        $path = $this->getPathToPdf($pageId);
        if ($path) {
            $result = file_exists($path);
        }

        return $result;
    }

    /**
     * Return the PDF file saved in server for the CMS page
     *
     * @param int|string  $pageId            ID/code of the page
     * @param bool        $createIfNotExists if true, create PDF file if it exists not
     *
     * @return false|mixed
     */
    public function getCmsPagePdf($pageId, $createIfNotExists = false)
    {
        $result = false;

        $fileExists = $this->checkCmsPagePdf($pageId);
        if (!$fileExists && $createIfNotExists) {
            $this->saveCmsPagePdf($pageId);
            $fileExists = $this->checkCmsPagePdf($pageId);
        }

        if ($fileExists) {
            $result = file_get_contents($this->getPathToPdf($pageId));
        }

        return $result;
    }

    /**
     * Save the CMS page content as PDF file in server (media/cms_pages/identifier.pdf)
     *
     * @param int|string  $pageId      ID/code of the page
     * @param string|bool $htmlContent HTML content to be saved as PDF. If false, default content generation apply
     */
    public function saveCmsPagePdf($pageId, $htmlContent = false)
    {
        /** @var Mage_Cms_Model_Page $page */
        $page = Mage::getModel('cms/page')->load($pageId);

        if ($page->getId()) {
            if ($htmlContent === false) {
                $htmlContent = Mage::app()->getLayout()->createBlock('silex_cmsattachment/page')
                    ->setTemplate('silex/cmsattachment/page.phtml')
                    ->setPage($page)
                    ->toHtml();
            }

            $mpdf = new mPDF();
            $mpdf->WriteHTML($htmlContent);
            $mpdf->Output($this->getPathToPdf($pageId), 'F');
        }
    }
} 