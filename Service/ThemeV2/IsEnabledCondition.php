<?php

namespace Klevu\Categorynavigation\Service\ThemeV2;

use Klevu\Categorynavigation\Helper\Data as CategorynavigationHelper;
use Klevu\Categorynavigation\Model\System\Config\Source\Categorylandingoptions;
use Klevu\FrontendJs\Api\IsEnabledConditionInterface as FrontendJsIsEnabledConditionInterface;
use Klevu\Metadata\Api\IsEnabledConditionInterface as MetadataIsEnabledConditionInterface;
use Klevu\Search\Helper\Config as SearchConfigHelper;
use Klevu\Search\Model\Source\ThemeVersion;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class IsEnabledCondition implements
    FrontendJsIsEnabledConditionInterface,
    MetadataIsEnabledConditionInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function execute($storeId = null)
    {
        $isEnabled = (int)$this->scopeConfig->getValue(
            CategorynavigationHelper::XML_PATH_CATEGORY_LANDING_STATUS,
            ScopeInterface::SCOPE_STORES,
            $storeId
        ) === Categorylandingoptions::KLEVU_TEMPLATE_LAYOUT;

        $themeVersion = $this->scopeConfig->getValue(
            SearchConfigHelper::XML_PATH_THEME_VERSION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $isEnabled && ($themeVersion === ThemeVersion::V2);
    }
}
