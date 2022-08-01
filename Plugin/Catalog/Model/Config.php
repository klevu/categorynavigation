<?php

namespace Klevu\Categorynavigation\Plugin\Catalog\Model;

use Klevu\Categorynavigation\Helper\Config as KlevuCatConfig;
use Klevu\Categorynavigation\Helper\Data as KlevuCatData;
use Klevu\Search\Helper\Config as Klevu_SearchConfigHelper;
use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template as TemplateOverride;
use Magento\Store\Model\StoreManagerInterface;
use Klevu\Categorynavigation\Constants;

/**
 * Class Config
 * @package Klevu\Categorynavigation\Plugin\Catalog\Model
 */
class Config
{

    /**
     * @deprecated
     * @see \Klevu\Categorynavigation\Constants::KLEVU_PRESERVE_LAYOUT
     */
    const KLEVU_PRESERVE_LAYOUT = Constants::KLEVU_PRESERVE_LAYOUT;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var KlevuCatConfig
     */
    private $_klevuCatConfig;
    /**
     * @var KlevuCatData
     */
    private $_searchHelper;
    /**
     * @var TemplateOverride
     */
    private $_templateOverride;
    /**
     * @var Klevu_SearchConfigHelper|mixed
     */
    private $klevuSearchConfigHelper;
    /**
     * @var Registry|mixed
     */
    private $registry;

    /**
     * Config constructor.
     * @param StoreManagerInterface $storeManager
     * @param KlevuCatConfig $klevuCatConfig
     * @param KlevuCatData $searchHelper
     * @param TemplateOverride $templateOverride
     * @param Klevu_SearchConfigHelper|null $klevuSearchConfigHelper
     * @param Registry|null $registry
     */
    public function __construct(
        StoreManagerInterface    $storeManager,
        KlevuCatConfig           $klevuCatConfig,
        KlevuCatData             $searchHelper,
        TemplateOverride         $templateOverride,
        Klevu_SearchConfigHelper $klevuSearchConfigHelper = null,
        Registry                 $registry = null
    )
    {
        $this->_storeManager = $storeManager;
        $this->_klevuCatConfig = $klevuCatConfig;
        $this->_searchHelper = $searchHelper;
        $this->_templateOverride = $templateOverride;
        $this->registry = $registry ?: ObjectManager::getInstance()->get(Registry::class);
        $this->klevuSearchConfigHelper = $klevuSearchConfigHelper ?: ObjectManager::getInstance()->get(Klevu_SearchConfigHelper::class);
    }

    /**
     * Adding custom options and changing labels
     *
     * @param CatalogConfig $catalogConfig
     * @param array $options
     *
     * @return array
     */
    public function afterGetAttributeUsedForSortByArray(CatalogConfig $catalogConfig, $options)
    {
        try {
            $layout = $this->_templateOverride->getLayout();
            $block = $layout->getBlock('category.products.list');
        } catch (LocalizedException $e) {
            return $options;
        }
        $currentStoreId = $this->getCurrentStoreId();
        if (!$this->klevuSearchConfigHelper->isExtensionConfigured($currentStoreId)) {
            return $options;
        }

        if ($block && $this->_klevuCatConfig->getCategoryNavigationRelevance($currentStoreId) &&
            (int)$this->_searchHelper->categoryLandingStatus() === Constants::KLEVU_PRESERVE_LAYOUT
        ) {
            //Remove specific default sorting options
            unset($options['position']);
            //Changing label
            $customOption['personalized'] = $this->getKlevuCatRelevanceLabel();
            //Merge default sorting options with custom options
            $options = array_merge($customOption, $options);

            /** @var Category $currentCategory */
            $currentCategory = $this->registry->registry('current_category');
            if (!$currentCategory || !$currentCategory->getData('default_sort_by')) {
                $block->setDefaultDirection(ProductList::DEFAULT_SORT_DIRECTION);
            }
        }

        return $options;
    }

    /**
     * @return int
     */
    private function getCurrentStoreId()
    {
        $return = 0;
        try {
            $store = $this->_storeManager->getStore();
            $return = (int)$store->getId();
        } catch (NoSuchEntityException $e) {
            //TODO: Replace with Klevu_Search only if we release before 2.4.0
            $this->logger->error($e->getMessage(), ['originalException' => $e]);
        }
        return $return;
    }

    /**
     *  Returns the text label for category pages
     *
     * @return \Magento\Framework\Phrase|string
     */
    protected function getKlevuCatRelevanceLabel()
    {
        return $this->_klevuCatConfig->getCategoryPagesRelevanceLabel($this->getCurrentStoreId());
    }
}

