<?php

namespace Klevu\Categorynavigation\Block\Product\View;

use Magento\Catalog\Model\Category;

class Tracking extends \Magento\Framework\View\Element\Template
{
    const KLEVU_PRESERVE_LAYOUT = 2;
    const MAGENTO_DEFAULT = 1;
    const KLEVU_TEMPLATE_LAYOUT = 3;

    protected $registry;
    protected $storeManagerInterface;
    protected $customerGroupCollection;
    protected $stockHelper;
    protected $configHelper;
    protected $priceHelper;
    protected $searchHelperData;
    protected $remoteAddress;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Klevu\Search\Helper\Stock $stockHelper,
        \Klevu\Search\Helper\Config $configHelper,
        \Klevu\Search\Helper\Price $priceHelper,
        \Klevu\Search\Helper\Data $searchHelperData,
        \Klevu\Categorynavigation\Helper\Data $navigationHelper,
        \Magento\Framework\App\Request\Http $klevuhttp,
        \Klevu\Categorynavigation\Helper\Config $navigationConfigHelper,
        \Magento\Framework\App\Response\RedirectInterface $redirectInterface,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = []
    )
    {
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
     * @throws Exception
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
                'klevu_sessionId' => ""
            ];
            return json_encode($categoryViewProducts);
        } catch (\Exception $e) {
            $this->_searchHelperData->log(\Zend\Log\Logger::CRIT, sprintf("Exception thrown in %s::%s - %s", __CLASS__, __METHOD__, $e->getMessage()));
        }
    }

    /**
     * Check klevu configured or nor
     * @return boolean
     */
    public function isExtensionConfigured()
    {
        return $this->_configHelper->isExtensionConfigured();
    }


    /**
     * Get Store wise category navigation url
     * @return boolean
     */
    public function getCategoryNavigationTrackingUrl($store = null)
    {
        return $this->_navigationConfigHelper->getCategoryNavigationTrackingUrl($store);
    }

    /**
     * Get previous page router page
     * @return string
     */
    public function getRefererRoute($store = null)
    {
        return $this->_requestInterface->getRouteName();
    }

    /**
     * Check klevu presevelayout template selected for category navigation
     * @return boolean
     */
    public function checkPreserveLayout()
    {
        if ($this->_navigationHelper->categoryLandingStatus() == static::KLEVU_PRESERVE_LAYOUT) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get from category path from referer category page
     * @return string
     */
    public function getCategoryName()
    {

        $refUrl = explode("/", $this->_redirectInterface->getRefererUrl());
        $cat_url = explode(".", end($refUrl));
        if (isset($cat_url)) {
            $categorys = $this->_categoryFactory->create()
                ->getCollection()
                ->addAttributeToFilter('url_key', $cat_url[0])
                ->addAttributeToSelect(['name', 'path']);
            return $categorys->getFirstItem();
        }

    }


    /**
     * Get category name from path 2/3/4/5
     * @return string
     */
    public function getCategoryNameFormPath($path)
    {
        $pathIds = explode("/", $path);
        unset($pathIds[0]);
        unset($pathIds[1]);
        foreach ($pathIds as $key => $value) {
            $catnames[] = $this->getCategoryNameFromId($value);
        }

        if (!empty($catnames)) {
            $allCategoryNames = implode(";", $catnames);
            return $allCategoryNames;
        } else {
            return;
        }
    }

    /**
     * Get category name from id 4
     * @return string
     */
    public function getCategoryNameFromId($categoryId)
    {
        $category = $this->_categoryFactory->create()
            ->getCollection()
            ->addAttributeToFilter('entity_id', (int)$categoryId)
            ->addAttributeToSelect(['name', 'path'])
            ->getFirstItem();
        return $category->getName();
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
