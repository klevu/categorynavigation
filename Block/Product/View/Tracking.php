<?php

namespace Klevu\Categorynavigation\Block\Product\View;

use Klevu\Categorynavigation\Helper\Config as CatNavConfigHelper;
use Klevu\Categorynavigation\Helper\Data as CatNavHelper;
use Klevu\Logger\Constants as LoggerConstants;
use Klevu\Search\Helper\Config as SearchConfigHelper;
use Klevu\Search\Helper\Data as SearchHelper;
use Klevu\Search\Helper\Price as PriceHelepr;
use Klevu\Search\Helper\Stock as StockHelper;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class Tracking extends Template
{
    const KLEVU_PRESERVE_LAYOUT = 2;
    const MAGENTO_DEFAULT = 1;
    const KLEVU_TEMPLATE_LAYOUT = 3;
    const TRACKING_URL = '/analytics/categoryProductViewTracking';

    /**
     * @var mixed
     * @deprecated Not used
     * @see $_registry
     */
    protected $registry;
    /**
     * @var mixed
     * @deprecated Not used
     * @see $_storeManagerInterface
     */
    protected $storeManagerInterface;
    /**
     * @var mixed
     * @deprecated Not used
     * @see nothing
     *
     */
    protected $customerGroupCollection;
    /**
     * @var mixed
     * @deprecated Not used
     * @see $_stockHelper
     */
    protected $stockHelper;
    /**
     * @var mixed
     * @deprecated Not used
     * @see $_configHelper
     *
     */
    protected $configHelper;
    /**
     * @var mixed
     * @deprecated Not used
     * @see $_priceHelper
     */
    protected $priceHelper;
    /**
     * @var mixed
     * @deprecated Not used
     * @see $_searchHelperData
     */
    protected $searchHelperData;
    /**
     * @var mixed
     * @deprecated Not used
     * @see $_remoteAddress
     */
    protected $remoteAddress;
    /**
     * @var Registry
     */
    protected $_registry;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManagerInterface;
    /**
     * @var StockHelper
     */
    protected $_stockHelper;
    /**
     * @var SearchConfigHelper
     */
    protected $_configHelper;
    /**
     * @var PriceHelepr
     */
    protected $_priceHelper;
    /**
     * @var SearchHelper
     */
    protected $_searchHelperData;
    /**
     * @var CatNavHelper
     */
    protected $_navigationHelper;
    /**
     * @var HttpRequest
     */
    protected $_klevuhttp;
    /**
     * @var CatNavConfigHelper
     */
    protected $_navigationConfigHelper;
    /**
     * @var RequestInterface
     */
    protected $_requestInterface;
    /**
     * @var RedirectInterface
     */
    protected $_redirectInterface;
    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param StockHelper $stockHelper
     * @param SearchConfigHelper $configHelper
     * @param PriceHelepr $priceHelper
     * @param SearchHelper $searchHelperData
     * @param CatNavHelper $navigationHelper
     * @param HttpRequest $klevuhttp
     * @param CatNavConfigHelper $navigationConfigHelper
     * @param RedirectInterface $redirectInterface
     * @param CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StockHelper $stockHelper,
        SearchConfigHelper $configHelper,
        PriceHelepr $priceHelper,
        SearchHelper $searchHelperData,
        CatNavHelper $navigationHelper,
        HttpRequest $klevuhttp,
        CatNavConfigHelper $navigationConfigHelper,
        RedirectInterface $redirectInterface,
        CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_storeManagerInterface = $context->getStoreManager();
        $this->_stockHelper = $stockHelper;
        $this->_configHelper = $configHelper;
        $this->_priceHelper = $priceHelper;
        $this->_searchHelperData = $searchHelperData;
        $this->_klevuhttp = $klevuhttp;
        $this->_navigationHelper = $navigationHelper;
        $this->_navigationConfigHelper = $navigationConfigHelper;
        $this->_requestInterface = $context->getRequest();
        $this->_redirectInterface = $redirectInterface;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context, $data);
    }

    /**
     * JSON of required tracking parameter for Klevu Product Click Tracking, based on current product
     * @return string
     */
    public function getJsonTrackingData()
    {
        try {
            // Get the category
            $category = $this->_registry->registry('current_category');
            if (!$category instanceof Category) {
                return false;
            }

            $store = $this->_storeManagerInterface->getStore();
            $js_api_key = $this->_configHelper->getJsApiKey();
            $categoryViewProducts = [
                'klevu_apiKey' => $js_api_key,
                'klevu_categoryName' => $category->getName(),
                'klevu_categoryPath' => $this->getCategoryNameFormPath($category->getPath()),
                'klevu_shopperIP' => "",
                'klevu_loginCustomerEmail' => "",
                'klevu_sessionId' => "",
            ];

            return json_encode($categoryViewProducts);
        } catch (\Exception $e) {
            $this->_searchHelperData->log(
                LoggerConstants::ZEND_LOG_CRIT,
                sprintf("Exception thrown in %s::%s - %s", __CLASS__, __METHOD__, $e->getMessage())
            );
        }
    }

    /**
     * Check klevu configured or nor
     * @return bool
     */
    public function isExtensionConfigured()
    {
        return $this->_configHelper->isExtensionConfigured();
    }

    /**
     * Get Store wise category navigation url
     *
     * @param StoreInterface|string|int|null $store
     *
     * @return string
     */
    public function getCategoryNavigationTrackingUrl($store = null)
    {
        $protocol = $this->getRequest()->isSecure() ? 'https://' : 'http://';

        return $protocol .
            $this->_navigationConfigHelper->getCategoryNavigationTrackingUrl($store) .
            static::TRACKING_URL;
    }

    /**
     * Get previous page router page
     *
     * @param StoreInterface|string|int|null $store
     *
     * @return string
     */
    public function getRefererRoute($store = null)
    {
        return $this->_requestInterface->getRouteName();
    }

    /**
     * Check klevu presevelayout template selected for category navigation
     * @return bool
     */
    public function checkPreserveLayout()
    {
        return (int)$this->_navigationHelper->categoryLandingStatus() === static::KLEVU_PRESERVE_LAYOUT;
    }

    /**
     * WARNING: This badly named method does not return category name but the category!
     * Get from category path from referer category page
     * @return CategoryInterface|DataObject|null
     * @throws LocalizedException
     */
    public function getCategoryName()
    {
        $refUrl = explode("/", $this->_redirectInterface->getRefererUrl());
        $cat_url = explode(".", end($refUrl));
        if (isset($cat_url)) {
            return null;
        }
        $category = $this->_categoryFactory->create();
        /** @var CategoryCollection $collection */
        $collection = $category->getCollection();
        if ($collection) {
            $collection->addAttributeToFilter('url_key', $cat_url[0]);
            $collection->addAttributeToSelect(['name', 'path']);
        }

        return $collection ? $collection->getFirstItem(): null;
    }

    /**
     * Get category name from path 2/3/4/5
     *
     * @param string $path
     *
     * @return string
     */
    public function getCategoryNameFormPath($path)
    {
        $pathIds = explode("/", $path);
        unset($pathIds[0], $pathIds[1]);
        $catNames = [];
        foreach ($pathIds as $value) {
            $catNames[] = $this->getCategoryNameFromId($value);
        }

        return implode(";", $catNames);
    }

    /**
     * Get category name from id 4
     *
     * @param string|int $categoryId
     *
     * @return string
     */
    public function getCategoryNameFromId($categoryId)
    {
        $firstCategory = null;
        $category = $this->_categoryFactory->create();
        $collection = $category->getCollection();
        if ($collection) {
            $collection->addAttributeToFilter('entity_id', (int)$categoryId);
            $collection->addAttributeToSelect(['name', 'path']);
            $firstCategory = $collection->getFirstItem();
        }

        return $firstCategory ? $firstCategory->getName(): '';
    }

    /**
     * Get current controller name
     * @return string
     */
    public function getCurrentController()
    {
        return $this->_klevuhttp->getControllerName();
    }

    /**
     * Get current category navigation version
     * @return string
     */
    public function getModuleInfo()
    {
        return $this->_configHelper->getModuleInfoCatNav();
    }
}
