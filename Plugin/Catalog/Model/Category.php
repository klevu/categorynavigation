<?php

namespace Klevu\Categorynavigation\Plugin\Catalog\Model;

use Klevu\Categorynavigation\Helper\Config as KlevuCatConfig;
use Klevu\Categorynavigation\Helper\Data as KlevuCatData;
use Klevu\Search\Helper\Config as SearchConfigHelper;
use Klevu\Categorynavigation\Constants;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Psr\Log\LoggerInterface;

class Category
{

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var KlevuCatConfig
     */
    private $klevuCatConfig;
    /**
     * @var KlevuCatData
     */
    private $klevuCatData;
    /**
     * @var Template
     */
    private $templateOverride;
    /**
     * @var SearchConfigHelper|mixed
     */
    private $klevuSearchConfigHelper;
    /**
     * @var Registry
     */
    private $registry;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param StoreManagerInterface $storeManager
     * @param KlevuCatConfig $klevuCatConfig
     * @param KlevuCatData $klevuCatData
     * @param Template $templateOverride
     * @param SearchConfigHelper $klevuSearchConfigHelper
     * @param Registry $registry
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        KlevuCatConfig $klevuCatConfig,
        KlevuCatData $klevuCatData,
        Template $templateOverride,
        SearchConfigHelper $klevuSearchConfigHelper,
        Registry $registry,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->klevuCatConfig = $klevuCatConfig;
        $this->klevuCatData = $klevuCatData;
        $this->templateOverride = $templateOverride;
        $this->registry = $registry;
        $this->klevuSearchConfigHelper = $klevuSearchConfigHelper;
        $this->logger = $logger;
    }

    /**
     * Adding custom options and changing labels
     *
     * @param $catalogCategory $catalogCategory
     * @param array $availableSortBy
     *
     * @return array
     */
    public function afterGetAvailableSortByOptions(CategoryInterface $category, array $availableSortBy)
    {
        try {
            $layout = $this->templateOverride->getLayout();
            $block = $layout->getBlock('category.products.list');
        } catch (LocalizedException $e) {
            return $availableSortBy;
        }
        $currentStoreId = $this->getCurrentStoreId();
        if (!$this->klevuSearchConfigHelper->isExtensionConfigured($currentStoreId)) {
            return $availableSortBy;
        }

        if ($block && $this->klevuCatConfig->getCategoryNavigationRelevance($currentStoreId)
            && (int)$this->klevuCatData->categoryLandingStatus() === Constants::KLEVU_PRESERVE_LAYOUT
        ) {
            //Remove specific default sorting options
            if (isset($availableSortBy['position'])) {
                unset($availableSortBy['position']);
            }
            //Changing label
            $customOption['personalized'] = $this->getKlevuCatRelevanceLabel();
            //Merge default sorting options with custom options
            $availableSortBy = array_merge($customOption, $availableSortBy);
        }

        return $availableSortBy;
    }

    /**
     * @return int
     */
    private function getCurrentStoreId()
    {
        $return = 0;
        try {
            $store = $this->storeManager->getStore();
            $return = (int)$store->getId();
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage(), ['originalException' => $e]);
        }

        return $return;
    }

    /**
     *  Returns the text label for category pages
     *
     * @return \Magento\Framework\Phrase
     */
    private function getKlevuCatRelevanceLabel()
    {
        return $this->klevuCatConfig->getCategoryPagesRelevanceLabel($this->getCurrentStoreId());
    }
}

