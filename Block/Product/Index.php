<?php
/**
 * Copyright © 2015 Dd . All rights reserved.
 */
namespace Klevu\Categorynavigation\Block\Product;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $_registry;
	
	protected $_categorynavigationHelper;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Framework\Registry $registry,
		\Klevu\Categorynavigation\Helper\Config $categorynavigationHelper,
        array $data = []
    )
    {
        $this->_registry = $registry;
		$this->_categorynavigationHelper = $categorynavigationHelper;
        parent::__construct($context, $data);
    }
    public function getCurrentCategory()
    {        
        return $this->_registry->registry('current_category');
    }

    public function getCategoryName($categoryId)
    {
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $category = $_objectManager->create('Magento\Catalog\Model\Category')
            ->load($categoryId);
        $categoryName = $category->getName();
        return $categoryName;
    }
}
