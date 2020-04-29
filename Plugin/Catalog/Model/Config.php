<?php

namespace Klevu\Categorynavigation\Plugin\Catalog\Model;

use Magento\Store\Model\StoreManagerInterface;
use Klevu\Categorynavigation\Helper\Config as KlevuCatConfig;
use Klevu\Categorynavigation\Helper\Data as KlevuCatData;
use Magento\Framework\View\Element\Template as TemplateOverride;
class Config
{

    protected $_storeManager;

    const KLEVU_PRESERVE_LAYOUT    = 2;

    public function __construct(
        StoreManagerInterface $storeManager,
        KlevuCatConfig $klevuCatConfig,
        KlevuCatData $searchHelper,
        TemplateOverride $templateOverride

    ) {
        $this->_templateOverride = $templateOverride;
        $this->_storeManager = $storeManager;
        $this->_klevuCatConfig = $klevuCatConfig;
        $this->_searchHelper = $searchHelper;

    }

    /**
     * Adding custom options and changing labels
     *
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param [] $options
     * @return []
     */
    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $catalogConfig, $options)
    {
        $block = $this->_templateOverride->getLayout()->getBlock('category.products.list');
        if($block && $this->_klevuCatConfig->getCategoryNavigationRelevance($this->_storeManager->getStore()) && $this->_searchHelper->categoryLandingStatus() == static::KLEVU_PRESERVE_LAYOUT) {
            //Remove specific default sorting options
            unset($options['position']);
            //Changing label
            $customOption['personalized'] = $this->getKlevuCatRelevanceLabel();
            //Merge default sorting options with custom options
            $options = array_merge($customOption, $options);

            $block->setDefaultDirection('desc');

            return $options;
        }
        return $options;
    }

     /** Returns the text label for category pages
     *
     * @return \Magento\Framework\Phrase|string
     */
    protected function getKlevuCatRelevanceLabel()
    {
        return $this->_klevuCatConfig->getCategoryPagesRelevanceLabel($this->_storeManager->getStore());
    }
}
   

