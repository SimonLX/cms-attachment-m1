<?php

/**
 * Class Silex_CmsAttachment_Block_Page
 */
class Silex_CmsAttachment_Block_Page extends Mage_Core_Block_Template
{
    /**
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