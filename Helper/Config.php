<?php

namespace Klevu\Categorynavigation\Helper;

use Klevu\Search\Helper\Api as ApiHelper;
use Klevu\Search\Helper\Data as SearchHelper;
use Klevu\Search\Model\Api\Action\Features as ApiActionFeatures;
use Klevu\Search\Service\Account\UpdateEndpoints;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Config\Model\ResourceModel\Config as Magento_Config;
use Magento\Framework\App\Config\ReinitableConfigInterface;

class Config extends AbstractHelper
{
    const XML_PATH_CATEGORY_NAVIGATION_URL = UpdateEndpoints::XML_PATH_CATEGORY_NAVIGATION_URL;
    const XML_PATH_CATEGORY_NAVIGATION_TRACKING_URL = UpdateEndpoints::XML_PATH_CATEGORY_NAVIGATION_TRACKING_URL;
    const XML_PATH_CATEGORY_RELEVANCE = "klevu_search/categorylanding/klevu_cat_relevance";
    const XML_PATH_CATEGORY_KLEVU_RELEVANCE_LABEL = "klevu_search/categorylanding/relevance_label";
    const XML_PATH_CATEGORY_LAZYLOAD = "klevu_search/developer/lazyload_js_catnav";
    const XML_PATH_CATEGORY_CONTENT_MIN_HEIGHT = "klevu_search/developer/content_min_height_catnav";

    /**
     * @var ScopeConfigInterface
     */
    protected $_appConfigScopeConfigInterface;

    /**
     * @var SearchHelper
     */
    protected $_searchHelperData;

    /**
     * @var UrlInterface
     */
    protected $_magentoFrameworkUrlInterface;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeModelStoreManagerInterface;

    /**
     * @var Store
     */
    protected $_frameworkModelStore;

    /**
     * @var ApiActionFeatures
     * @deprecated Not used
     */
    protected $_apiActionFeatures;

    /**
     * @var null
     * @deprecated Not used
     */
    protected $_klevu_features_response;

    /**
     * @var ConfigValue
     */
    protected $_modelConfigData;

    /**
     * @param ScopeConfigInterface $appConfigScopeConfigInterface
     * @param UrlInterface $magentoFrameworkUrlInterface
     * @param StoreManagerInterface $storeModelStoreManagerInterface
     * @param Store $frameworkModelStore
     * @param ConfigValue $modelConfigData
     * @param ResourceConnection $frameworkModelResource
     * @param Magento_Config $magentoResourceConfig
     * @param ReinitableConfigInterface $magentoReinitConfigInterface
     */
    public function __construct(
        ScopeConfigInterface $appConfigScopeConfigInterface,
        UrlInterface $magentoFrameworkUrlInterface,
        StoreManagerInterface $storeModelStoreManagerInterface,
        Store $frameworkModelStore,
        ConfigValue $modelConfigData,
        ResourceConnection $frameworkModelResource,
        Magento_Config $magentoResourceConfig,
        ReinitableConfigInterface $magentoReinitConfigInterface
    ) {
        $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
        $this->_magentoFrameworkUrlInterface = $magentoFrameworkUrlInterface;
        $this->_storeModelStoreManagerInterface = $storeModelStoreManagerInterface;
        $this->_frameworkModelStore = $frameworkModelStore;
        $this->_modelConfigData = $modelConfigData;
        $this->_frameworkModelResource = $frameworkModelResource;
        $this->_magentoResourceConfig = $magentoResourceConfig;
        $this->_magentoReinitConfigInterface = $magentoReinitConfigInterface;
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCategoryNavigationUrl($store = null)
    {
        $url = $this->_appConfigScopeConfigInterface->getValue(
            static::XML_PATH_CATEGORY_NAVIGATION_URL,
            ScopeInterface::SCOPE_STORE,
            $store
        );
        return $url ?: ApiHelper::ENDPOINT_DEFAULT_HOSTNAME;
    }

    /**
     * @param null $store
     * @return string
     */
    public function getCategoryNavigationTrackingUrl($store = null)
    {
        $url = $this->_appConfigScopeConfigInterface->getValue(
            static::XML_PATH_CATEGORY_NAVIGATION_TRACKING_URL,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $url ?: ApiHelper::ENDPOINT_DEFAULT_ANALYTICS_HOSTNAME;
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
     * @param string $url
     * @param Store|int|null $store
     *
     * @return void
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
     * @param string $key
     * @param string $value
     * @param Store|int|null $store If not given, current store will be used.
     *
     * @return $this
     */
    public function setStoreConfig($key, $value, $store = null)
    {
        $config = $this->_appConfigScopeConfigInterface;

        $scope_id = $this->_storeModelStoreManagerInterface->getStore($store)->getId();

        if ($scope_id !== null) {
            $this->_magentoResourceConfig->saveConfig($key, $value, "stores", $scope_id);
            $this->_resetConfig();
        }
        return $this;
    }

    /**
     * Clear config cache
     */
    protected function _resetConfig()
    {
        $this->_magentoReinitConfigInterface->reinit();
    }

    /**
     * @param Store|int|null $store
     * @return string
     */
    public function getCategoryNavigationRelevance($store = null)
    {
        return $this->_appConfigScopeConfigInterface->getValue(
            static::XML_PATH_CATEGORY_RELEVANCE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Return the Relevance label for category pages
     *
     * @param Store|int|null $store
     * @return string
     */
    public function getCategoryPagesRelevanceLabel($store = null)
    {
        $sortLabel = $this->_appConfigScopeConfigInterface->getValue(
            static::XML_PATH_CATEGORY_KLEVU_RELEVANCE_LABEL,
            ScopeInterface::SCOPE_STORE,
            $store
        );
        return $sortLabel ?: __('Relevance');
    }
}
