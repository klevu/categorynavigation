<?php

namespace Klevu\Categorynavigation\Block\Product;

use Klevu\Categorynavigation\Helper\Config as CatNavConfigHelper;
use Klevu\Categorynavigation\Helper\Data as CatNavHelper;
use Klevu\Logger\Constants as LoggerConstants;
use Klevu\Search\Helper\Config as SearchConfigHelper;
use Klevu\Search\Helper\Data as SearchHelper;
use Klevu\Search\Helper\Price as PriceHelper;
use Klevu\Search\Helper\Stock as StockHelper;
use Klevu\Search\Model\Attribute\Rating;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use \Magento\Framework\Registry;
use \Magento\Catalog\Model\Product;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class Tracking extends Template
{
    const KLEVU_PRESERVE_LAYOUT = 2;
    const MAGENTO_DEFAULT = 1;
    const KLEVU_TEMPLATE_LAYOUT = 3;
    const TRACKING_URL = '/analytics/categoryProductClickTracking';

    /**
     * @var mixed
     * @deprecated incorrectly named, is never used
     * @see $_registry
     */
    protected $registry;
    /**
     * @var mixed
     * @deprecated incorrectly named, is never used
     * @see $_storeManagerInterface
     */
    protected $storeManagerInterface;
    /**
     * @var mixed
     * @deprecated incorrectly named, is never used
     * @see $_customerSession
     */
    protected $customerSession;
    /**
     * @var mixed
     * @deprecated is never used
     * @see nothing
     */
    protected $customerGroupCollection;
    /**
     * @var mixed
     * @deprecated incorrectly named, is never used
     * @see $_stockHelper
     */
    protected $stockHelper;
    /**
     * @var mixed
     * @deprecated incorrectly named, is never used
     * @see $_configHelper
     */
    protected $configHelper;
    /**
     * @var mixed
     * @deprecated incorrectly named, is never used
     * @see $_priceHelper
     */
    protected $priceHelper;
    /**
     * @var mixed
     * @deprecated incorrectly named, is never used
     * @see $_searchHelperData
     */
    protected $searchHelperData;
    /**
     * @var mixed
     * @deprecated incorrectly named, is never used
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
     * @var PriceHelper
     */
    protected $_priceHelper;
    /**
     * @var CustomerSession
     */
    protected $_customerSession;
    /**
     * @var SearchHelper
     */
    protected $_searchHelperData;
    /**
     * @var RemoteAddress
     */
    protected $_remoteAddress;
    /**
     * @var CatNavHelper
     */
    protected $_navigationHelper;
    /**
     * @var CatNavConfigHelper
     */
    protected $_navigationConfigHelper;
    /**
     * @var Category
     */
    protected $_category;
    /**
     * @var RedirectInterface
     */
    protected $_redirectInterface;
    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;
    /**
     * @var RequestInterface
     */
    protected $_requestInterface;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param StockHelper $stockHelper
     * @param SearchConfigHelper $configHelper
     * @param PriceHelper $priceHelper
     * @param SearchHelper $searchHelperData
     * @param CatNavHelper $navigationHelper
     * @param CatNavConfigHelper $navigationConfigHelper
     * @param CustomerSession $customerSession
     * @param RemoteAddress $remoteAddress
     * @param Category $category
     * @param RedirectInterface $redirectInterface
     * @param CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StockHelper $stockHelper,
        SearchConfigHelper $configHelper,
        PriceHelper $priceHelper,
        SearchHelper $searchHelperData,
        CatNavHelper $navigationHelper,
        CatNavConfigHelper $navigationConfigHelper,
        CustomerSession $customerSession,
        RemoteAddress $remoteAddress,
        Category $category,
        RedirectInterface $redirectInterface,
        CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_storeManagerInterface = $context->getStoreManager();
        $this->_stockHelper = $stockHelper;
        $this->_configHelper = $configHelper;
        $this->_priceHelper = $priceHelper;
        $this->_customerSession = $customerSession;
        $this->_searchHelperData = $searchHelperData;
        $this->_remoteAddress = $remoteAddress;
        $this->_navigationHelper = $navigationHelper;
        $this->_navigationConfigHelper = $navigationConfigHelper;
        $this->_category = $category;
        $this->_redirectInterface = $redirectInterface;
        $this->_categoryFactory = $categoryFactory;
        $this->_requestInterface = $context->getRequest();
        parent::__construct($context, $data);
    }

    /**
     * JSON of required tracking parameter for Klevu Product Click Tracking, based on current product
     * @return string|null
     */
    public function getJsonTrackingData()
    {
        try {
            // Get the product
            $product = $this->_registry->registry('current_product');
            if (!$product instanceof Product) {
                return false;
            }

            $id = $product->getId();
            $store = $this->_storeManagerInterface->getStore();
            $js_api_key = $this->_configHelper->getJsApiKey();

            $name = $product->getName();
            $product_url = $product->getProductUrl();
            $product_sku = $product->getSku();

            if ($product->getData("type_id") === Configurable::TYPE_CODE) {
                $parent = $product;
                $productTypeInstance = $product->getTypeInstance();
                $usedProducts = $productTypeInstance->getUsedProducts($product);
                foreach ($usedProducts as $child) {
                    $product_saleprice = $this->_priceHelper->getKlevuSalePrice($parent, $child, $store);
                    $product_sale_price = $product_saleprice['salePrice'];
                }
            } else {
                $parent = null;
                $product_saleprice = $this->_priceHelper->getKlevuSalePrice($parent, $product, $store);
                $product_sale_price = $product_saleprice['salePrice'];
            }
            //Sending the parent or child product id
            $klevu_productGroupId = $product->getId();
            $klevu_productVariantId = $product->getId();

            $rating = $product->getDataUsingMethod(Rating::ATTRIBUTE_CODE);
            $product = [
                'klevu_apiKey' => $js_api_key,
                'klevu_productId' => $id,
                'klevu_productName' => $name,
                'klevu_productUrl' => $product_url,
                'klevu_productSku' => $product_sku,
                'klevu_salePrice' => $product_sale_price,
                'klevu_productRatings' => is_numeric($rating)
                    ? $this->convertToRatingStar((float)$rating)
                    : null,
                'klevu_productGroupId' => $klevu_productGroupId,
                'klevu_productVariantId' => $klevu_productVariantId,
            ];

            return json_encode($product);
        } catch (\Exception $e) {
            $this->_searchHelperData->log(
                LoggerConstants::ZEND_LOG_CRIT,
                sprintf("Exception thrown in %s::%s - %s", __CLASS__, __METHOD__, $e->getMessage())
            );
        }

        return null;
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
     * Check klevu presevelayout template selected for category navigation
     * @return bool
     */
    public function checkPreserveLayout()
    {
        return (int)$this->_navigationHelper->categoryLandingStatus() === static::KLEVU_PRESERVE_LAYOUT;
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
     * @param mixed|null $store
     *
     * @return string|null
     */
    public function getRefererRoute($store = null)
    {
        return $this->_requestInterface->getRouteName();
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
        if (!$cat_url) {
            return null;
        }
        $category = $this->_categoryFactory->create();
        /** @var CategoryCollection $collection */
        $collection = $category->getCollection();
        if ($collection) {
            $collection->addAttributeToFilter('url_key', $cat_url[0]);
            $collection->addAttributeToSelect(['name', 'path']);
        }

        return $collection ? $collection->getFirstItem() : null;
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

        return $firstCategory ? $firstCategory->getName() : '';
    }

    /**
     * Convert percent to rating star
     *
     * @param float|int $percentage
     *
     * @return float|null
     */
    public function convertToRatingStar($percentage)
    {
        if (empty($percentage)) {
            return null;
        }

        // eg 20% * 0.05 = 1 star; 29% * 0.05 = 1 (rounded); 30% * 0.05 = 2 (rounded)
        return round($percentage * 0.05, 2);
    }
}
