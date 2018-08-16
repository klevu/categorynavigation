<?php
namespace Klevu\Categorynavigation\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CATEGORY_LANDING_STATUS   = "klevu_search/categorylanding/enabledcategorynavigation";
    const XML_PATH_CATEGORY_RESULT_COUNT   = "klevu_search/categorylanding/max_no_of_products";
	const XML_PATH_CATEGORY_NAVIGATION_URL   = "klevu_search/general/category_navigation_url";
    protected $_appConfigScopeConfigInterface;
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $appConfigScopeConfigInterface
    ) {
        $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;  
    }
    
    /**
     * Check if the Category Listing page is enabled in the system configuration for the current store.
     *
     * @param $store_id
     *
     * @return bool
     */
    public function categoryLandingStatus($store = null) {
        return $this->_appConfigScopeConfigInterface->getValue(static::XML_PATH_CATEGORY_LANDING_STATUS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    public function getNoOfResults($store = null) {
    	return $this->_appConfigScopeConfigInterface->getValue(static::XML_PATH_CATEGORY_RESULT_COUNT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
	
	public function getCategoryNavigationUrl($store = null) {
    	return $this->_appConfigScopeConfigInterface->getValue(static::XML_PATH_CATEGORY_NAVIGATION_URL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
    
}
