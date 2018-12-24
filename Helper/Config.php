<?php

namespace Klevu\Categorynavigation\Helper;

use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\UrlInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Klevu\Search\Model\Product\Sync;
use \Magento\Framework\Model\Store;
use \Klevu\Search\Model\Api\Action\Features;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_frameworkAppRequestInterface;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_appConfigScopeConfigInterface;

    /**
     * @var \Klevu\Search\Helper\Data
     */
    protected $_searchHelperData;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_magentoFrameworkUrlInterface;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeModelStoreManagerInterface;
    
    /**
     * @var \Magento\Store\Model\Store
     */
    protected $_frameworkModelStore;

    /**
     * @var \Klevu\Search\Model\Api\Action\Features
     */
    protected $_apiActionFeatures;
    
    protected $_klevu_features_response;
    
    /**
     * @var \Magento\Framework\Config\Data
     */
    protected $_modelConfigData;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $appConfigScopeConfigInterface,
        \Magento\Framework\UrlInterface $magentoFrameworkUrlInterface,
        \Magento\Store\Model\StoreManagerInterface $storeModelStoreManagerInterface,
        \Magento\Framework\App\RequestInterface $frameworkAppRequestInterface,
        \Magento\Store\Model\Store $frameworkModelStore,
        \Magento\Framework\App\Config\Value $modelConfigData,
        \Magento\Framework\App\ResourceConnection $frameworkModelResource
    ) {
    
        $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
        $this->_magentoFrameworkUrlInterface = $magentoFrameworkUrlInterface;
        $this->_storeModelStoreManagerInterface = $storeModelStoreManagerInterface;
        $this->_frameworkAppRequestInterface = $frameworkAppRequestInterface;
        $this->_frameworkModelStore = $frameworkModelStore;
        $this->_modelConfigData = $modelConfigData;
        $this->_frameworkModelResource = $frameworkModelResource;
    }


    const XML_PATH_CATEGORY_NAVIGATION_URL = "klevu_search/general/category_navigation_url";
	const XML_PATH_CATEGORY_NAVIGATION_TRACKING_URL = "klevu_search/general/category_navigation_tracking_url";
    const XML_PATH_CATEGORY_RELEVANCE = "klevu_search/categorylanding/klevu_cat_relevance";

    


    /**
     * @param null $store
     * @return string
     */
    public function getCategoryNavigationUrl($store = null)
    {
        $url = $this->_appConfigScopeConfigInterface->getValue(static::XML_PATH_CATEGORY_NAVIGATION_URL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return ($url) ? $url : \Klevu\Search\Helper\Api::ENDPOINT_DEFAULT_HOSTNAME;
    }
	
	/**
     * @param null $store
     * @return string
     */
    public function getCategoryNavigationTrackingUrl($store = null)
    {
        $url = $this->_appConfigScopeConfigInterface->getValue(static::XML_PATH_CATEGORY_NAVIGATION_TRACKING_URL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return ($url) ? $url : \Klevu\Search\Helper\Api::ENDPOINT_DEFAULT_HOSTNAME;
    }
	
	
	/**
     * @param $url
     * @param null $store
     * @return $this
     */
    public function setCategoryNavigationTrackingUrl($url, $store = null)
    {

        $path = static::XML_PATH_CATEGORY_NAVIGATION_TRACKING_URL;
        $this->setStoreConfig($path, $url, $store);
        return $this;
    }

    /**
     * @param $url
     * @param null $store
     * @return $this
     */
    public function setCategoryNavigationUrl($url, $store = null)
    {

        $path = static::XML_PATH_CATEGORY_NAVIGATION_URL;
        $this->setStoreConfig($path, $url, $store);
        return $this;
    }
	
	
	/**
     * Set category navigation url using sql.
     *
     * @param url|string
     * @param \Magento\Framework\Model\Store|int|null $store
     *
     * @return $this
     */
    public function setCategoryNavigationUrlSQL($url, $store = null)
    {
		$path = static::XML_PATH_CATEGORY_NAVIGATION_URL;
		$write =  $this->_frameworkModelResource->getConnection("core_write");
        $query = "replace into ".$this->_frameworkModelResource->getTableName('core_config_data')
                           . "(scope, scope_id, path, value) values "
                           . "(:scope, :scope_id, :path, :value)";
					
        $binds = 
				[
                    'scope' => "stores",
                    'scope_id' => $store,
                    'path' => $path,
                    'value'  => $url
                ];
        $write->query($query, $binds);
    }
    
	
	 /**
     * Set the store scope System Configuration value for the given key.
     *
     * @param string                         $key
     * @param string                         $value
     * @param \Magento\Framework\Model\Store|int|null $store If not given, current store will be used.
     *
     * @return $this
     */
    public function setStoreConfig($key, $value, $store = null)
    {
        $config = $this->_appConfigScopeConfigInterface;

        $config = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Config\Model\ResourceModel\Config');
        $scope_id = $this->_storeModelStoreManagerInterface->getStore($store)->getId();

        if ($scope_id !== null) {
            $config->saveConfig($key, $value, "stores", $scope_id);
            $this->_resetConfig();
        }
        return $this;
    }
	
	/**
     * Clear config cache
     */
    protected function _resetConfig()
    {
        \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\Config\ReinitableConfigInterface')->reinit();
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCategoryNavigationRelevance($store = null)
    {
        return $this->_appConfigScopeConfigInterface->getValue(static::XML_PATH_CATEGORY_RELEVANCE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        
    }


}
