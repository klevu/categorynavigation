<?php

namespace Klevu\Categorynavigation\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_CATEGORY_LANDING_STATUS = "klevu_search/categorylanding/enabledcategorynavigation";
    const XML_PATH_CATEGORY_RESULT_COUNT = "klevu_search/categorylanding/max_no_of_products";
    const XML_PATH_CATEGORY_NAVIGATION_URL = "klevu_search/general/category_navigation_url";

    /**
     * @var ScopeConfigInterface
     */
    protected $_appConfigScopeConfigInterface;

    /**
     * @param ScopeConfigInterface $appConfigScopeConfigInterface
     */
    public function __construct(
        ScopeConfigInterface $appConfigScopeConfigInterface
    ) {
        $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
    }

    /**
     * Check if the Category Listing page is enabled in the system configuration for the current store.
     *
     * @param StoreInterface|string|int|null $store
     *
     * @return mixed
     */
    public function categoryLandingStatus($store = null)
    {
        return $this->_appConfigScopeConfigInterface->getValue(
            static::XML_PATH_CATEGORY_LANDING_STATUS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param StoreInterface|string|int|null $store
     *
     * @return mixed
     */
    public function getNoOfResults($store = null)
    {
        return $this->_appConfigScopeConfigInterface->getValue(
            static::XML_PATH_CATEGORY_RESULT_COUNT,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param StoreInterface|string|int|null $store
     *
     * @return mixed
     */
    public function getCategoryNavigationUrl($store = null)
    {
        return $this->_appConfigScopeConfigInterface->getValue(
            static::XML_PATH_CATEGORY_NAVIGATION_URL,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
