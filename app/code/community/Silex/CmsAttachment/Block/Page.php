<?php

/**
 * Class Silex_CmsAttachment_Block_Page
 *
 * Block class for CMS page print view
 */
class Silex_CmsAttachment_Block_Page extends Mage_Core_Block_Template
{
    /**
     * Get page lang
     *
     * @return string
     */
    public function getLang()
    {
        if (!$this->hasData('lang')) {
            $this->setData('lang', substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2));
        }

        return $this->getData('lang');
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getPageTitle()
    {
        $title = '';
        $page = $this->getPage();

        if ($page && $page->getId()) {
            $title = $page->getTitle();
        }

        return $title;
    }

    /**
     * Get page heading
     *
     * @return string
     */
    public function getPageHeading()
    {
        $heading = '';
        $page = $this->getPage();

        if ($page && $page->getId()) {
            $heading = $page->getContentHeading();
        }

        return $heading;
    }

    /**
     * Get page content
     *
     * @return string
     */
    public function getPageContent()
    {
        $content = '';
        $page = $this->getPage();

        if ($page && $page->getId()) {
            $content = $page->getContent();
        }

        return $content;
    }
}
