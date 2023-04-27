<?php

namespace Klevu\Categorynavigation\Block\Product;

use Klevu\Categorynavigation\Helper\Config as CatNavConfigHelper;
use Klevu\Search\Helper\Config as KlevuConfig;
use Klevu\Search\Model\Source\ThemeVersion;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class Index extends Template
{
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var CatNavConfigHelper
     */
    protected $_categorynavigationHelper;
    /**
     * @var KlevuConfig
     */
    protected $_klevuConfig;
    /**
     * @var mixed
     */
    protected $_category;
    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param CategoryFactory $categoryFactory
     * @param CatNavConfigHelper $categorynavigationHelper
     * @param KlevuConfig $klevuConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CategoryFactory $categoryFactory,
        CatNavConfigHelper $categorynavigationHelper,
        KlevuConfig $klevuConfig,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_categoryFactory = $categoryFactory;
        $this->_categorynavigationHelper = $categorynavigationHelper;
        $this->_klevuConfig = $klevuConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return CategoryInterface|null
     */
    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    /**
     * @param string|int $categoryId
     *
     * @return string
     */
    public function getCategoryName($categoryId)
    {
        $category = $this->_categoryFactory->create();
        $category->load((int)$categoryId);

        return $category->getName();
    }

    /**
     * @return bool
     */
    public function isCustomerGroupPriceEnabled()
    {
        return $this->_klevuConfig->isCustomerGroupPriceEnabled();
    }

    /**
     * @return string
     */
    public function getJsUrl()
    {
        return $this->_klevuConfig->getJsUrl();
    }

    /**
     * Retrieve default per page values
     *
     * @return string[]
     */
    public function getGridPerPageValues()
    {
        return explode(",", $this->_klevuConfig->getCatalogGridPerPageValues());
    }

    /**
     * Retrieve default per page on empty it will return 24
     *
     * @return int
     */
    public function getGridPerPage()
    {
        return $this->_klevuConfig->getCatalogGridPerPage() ?: 24;
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
