<?php

namespace Klevu\Categorynavigation\Model\Observer;

use Klevu\Categorynavigation\Helper\Data as KlevuCatNavHelper;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;

/**
 * Class UpdateCategoryPageLayout
 * @package Klevu\Categorynavigation\Model\Observer
 */
class UpdateCategoryPageLayout implements ObserverInterface
{
    const KLEVU_PRESERVE_LAYOUT = 2;
    const MAGENTO_DEFAULT = 1;
    const KLEVU_TEMPLATE_LAYOUT = 3;

    /**
     * @var Registry
     */
    private $_registry;

    /**
     * @var KlevuCatNavHelper
     */
    private $_searchHelper;

    /**
     * UpdateCategoryPageLayout constructor.
     * @param KlevuCatNavHelper $searchHelper
     * @param Registry $registry
     */
    public function __construct(
        KlevuCatNavHelper $searchHelper,
        Registry $registry,
        RequestInterface $request

    )
    {
        $this->_searchHelper = $searchHelper;
        $this->_registry = $registry;
        $this->_request = $request;
    }

    /**
     * Add handles to the page.
     *
     * @param Observer $observer
     * @event layout_load_before
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var LayoutInterface $layout */
        $action = $observer->getData('full_action_name');
        $layout = $observer->getData('layout');
        $helper = $this->_searchHelper;
        $klevuPreviewReqParam = $this->_request->getParam('klevu_templ_preview');

        if ($action !== "catalog_category_view") {
            return;
        }
        if ($helper->categoryLandingStatus() == static::KLEVU_TEMPLATE_LAYOUT) {
            $this->addHandleCatNav($layout);

        } else if ($helper->categoryLandingStatus() !== static::KLEVU_TEMPLATE_LAYOUT &&
            $klevuPreviewReqParam == 'klevu-template') {
            $this->addHandleCatNav($layout);
        }
    }

    /**
     * @param $layout
     * @return false
     */
    private function addHandleCatNav($layout)
    {
        //Instance check for current category
        $category = $this->_registry->registry('current_category');
        if (!$category instanceof CategoryModel) {
            return false;
        }

        $categoryDisplayMode = $category->getData('display_mode');
        if ($categoryDisplayMode != "PAGE") {

            $layout->unsetElement('category.products');
            $layout->unsetElement('category.products.list');
            $layout->unsetElement('category.product.type.details.renderers');
            $layout->unsetElement('category.product.addto');
            $layout->unsetElement('category.product.addto.compare');
            $layout->unsetElement('product_list_toolbar');
            $layout->unsetElement('product_list_toolbar_pager');
            $layout->unsetElement('category.product.addto.wishlist');
            $layout->unsetElement('catalog.leftnav');
            $layout->unsetElement('catalog.navigation.state');
            $layout->unsetElement('catalog.navigation.renderer');
            $layout->getUpdate()->addHandle('klevu_category_index');
        }
    }
}




