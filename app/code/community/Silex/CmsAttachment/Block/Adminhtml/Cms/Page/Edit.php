<?php

/**
 * Class Silex_CmsAttachment_Block_Adminhtml_Cms_Page_Edit
 *
 * Overridden to add print button on CMS page edit form
 */
class Silex_CmsAttachment_Block_Adminhtml_Cms_Page_Edit extends Mage_Adminhtml_Block_Cms_Page_Edit
{
    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->_isAllowedAction('print')) {
            $this->_addButton(
                'print',
                array(
                    'label'   => Mage::helper('silex_cmsattachment')->__('Print'),
                    'onclick' => 'setLocation(\'' . $this->getPrintUrl() . '\')'
                )
            );
        } else {
            $this->_removeButton('print');
        }
    }

    /**
     * Getter of url for "Print" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function getPrintUrl()
    {
        return $this->getUrl('*/*/print', array('id' => Mage::registry('cms_page')->getId()));
    }
}
