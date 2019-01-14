<?php
/**
 * Copyright © 2015 Dd . All rights reserved.
 */
namespace Klevu\Categorynavigation\Block\Product\View;

class Info extends \Magento\Framework\View\Element\Template
{
    protected $_registry;

    protected $customerSession;
	protected $_searchHelperData;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
		\Klevu\Search\Helper\Data $searchHelperData,
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->_customerSession = $customerSession;
		$this->_searchHelperData = $searchHelperData;
        parent::__construct($context, $data);
    }
    public function getCurrentIP()
    {
        //Stats will assign correct data
        return "";//$this->_searchHelperData->getIp();
    }
    public function getSessionId()
    {
        //Stats will assign correct data
        return "";//md5(session_id());
    }
    public function getLoggedInUser()
    {
        //TODO: move to js only
        return "";//$this->_customerSession->getCustomer()->getEmail();
    }

    
}
