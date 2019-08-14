<?php

namespace Klevu\Categorynavigation\Block\Product;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Session\Generic;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use \Magento\Framework\Registry;
use \Magento\Catalog\Model\Product;

class Tracking extends \Magento\Framework\View\Element\Template
{
    const KLEVU_PRESERVE_LAYOUT = 2;
    const MAGENTO_DEFAULT = 1;
    const KLEVU_TEMPLATE_LAYOUT = 3;

    protected $registry;
    protected $storeManagerInterface;
    protected $customerSession;
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
        \Klevu\Categorynavigation\Helper\Config $navigationConfigHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Catalog\Model\Category $category,
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
     * @return string
     * @throws Exception
     */
    public function getJsonTrackingData()
    {
        try{
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

			if ($product->getData("type_id") == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
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

			$product = [
				'klevu_apiKey' => $js_api_key,
				'klevu_productId' => $id,
				'klevu_productName' => $name,
				'klevu_productUrl' => $product_url,
				'klevu_productSku' => $product_sku,
				'klevu_salePrice' => $product_sale_price,
				'klevu_productRatings' => $this->convertToRatingStar($product->getRating())
				//'klevu_shopperIP' => $this->_searchHelperData->getIp(),
				//'klevu_loginCustomerEmail' => $this->_customerSession->getCustomer()->getEmail(),
				//'klevu_sessionId' => md5(session_id())
			];
			return json_encode($product);
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
     * Convert percent to rating star
     *
     * @param int percentage
     *
     * @return float
     */
    public function convertToRatingStar($percentage)
    {
        if (!empty($percentage) && $percentage != 0) {
            $start = $percentage * 5;
            return round($start / 100, 2);
        } else {
            return;
        }
    }


}