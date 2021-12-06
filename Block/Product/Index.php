<?php
/**
 * Copyright © 2015 Dd . All rights reserved.
 */
namespace Klevu\Categorynavigation\Block\Product;
use \Klevu\Search\Helper\Config;
use Klevu\Search\Helper\Config as KlevuConfig;
use Klevu\Search\Model\Source\ThemeVersion;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $_registry;

	protected $_categorynavigationHelper;

    protected  $_klevuConfig;

	protected $_category;



    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Klevu\Categorynavigation\Helper\Config $categorynavigationHelper,
        \Klevu\Search\Helper\Config $klevuConfig,
        array $data = []
    )
    {
        $this->_registry = $registry;
		$this->_categoryFactory = $categoryFactory;
		$this->_categorynavigationHelper = $categorynavigationHelper;
        $this->_klevuConfig = $klevuConfig;
        parent::__construct($context, $data);
    }

    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    public function getCategoryName($categoryId)
    {
        return $this->_categoryFactory->create()->load((int)$categoryId)->getName();
    }

    public function  isCustomerGroupPriceEnabled()
    {
        return  $this->_klevuConfig->isCustomerGroupPriceEnabled();
    }

	public function getJsUrl()
	{
		return $this->_klevuConfig->getJsUrl();
	}

	/**
     * Retrieve default per page values
     *
     * @return array
     */
	public function getGridPerPageValues()
	{
		//return explode(",",$this->_categorynavigationHelper->getCatalogGridPerPageValues());
        return explode(",",$this->_klevuConfig->getCatalogGridPerPageValues());
	}

	/**
     * Retrieve default per page on empty it will return 24
     *
     * @return int
     */
	public function getGridPerPage()
	{
		//return (int)$this->_categorynavigationHelper->getCatalogGridPerPage();
        return (int) $this->_klevuConfig->getCatalogGridPerPage() ?
            $this->_klevuConfig->getCatalogGridPerPage() : 24;
	}

    /**
     * {@inheritdoc}
     * @return string
     */
    protected function _toHtml()
    {
        try {
            $store = $this->_storeManager->getStore();
            $storeId = (int)$store->getId();
        } catch (NoSuchEntityException $e) {
            $this->_logger->error($e->getMessage());

            return '';
        }

        $themeVersion = $this->_scopeConfig->getValue(
            KlevuConfig::XML_PATH_THEME_VERSION,
            ScopeInterface::SCOPE_STORES,
            $storeId
        );

        if (ThemeVersion::V2 === $themeVersion) {
            return '';
        }

        return parent::_toHtml();
    }
}
