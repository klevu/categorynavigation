<?php

namespace Klevu\Categorynavigation\Observer\Backend;

use Klevu\Categorynavigation\Helper\Config as Klevu_HelperConfigCatNav;
use Klevu\Logger\Constants as LoggerConstants;
use Klevu\Search\Model\Klevu\HelperManager as Klevu_HelperManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @deprecated 2.11.1 - no longer required. Targeted config load rather than config save.
 * @see no direct alternative
 */
class SingleStoreViewConfigToShowForCatNav implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private $_request;
    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;
    /**
     * @var Klevu_HelperManager
     */
    private $_klevuHelperManager;
    /**
     * @var Klevu_HelperConfigCatNav
     */
    private $_klevuHelperConfigCatNav;

    /**
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     * @param Klevu_HelperManager $klevuHelperManager
     * @param Klevu_HelperConfigCatNav $KlevuHelperConfigCatNav
     */
    public function __construct(
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        Klevu_HelperManager $klevuHelperManager,
        Klevu_HelperConfigCatNav $KlevuHelperConfigCatNav
    ) {
        $this->_request = $request;
        $this->_storeManager = $storeManager;
        $this->_klevuHelperManager = $klevuHelperManager;
        $this->_klevuHelperConfigCatNav = $KlevuHelperConfigCatNav;
    }

    /**
     * @param EventObserver $observer
     *
     * @return void
     *
     * @deprecated 2.11.1 - no longer required. Targeted config load rather than config save.
     * @see no direct alternative. Changed adminhtml/system.xml fields to all have showInDefault="1"
     *       using group showInDefault to control visibility
     */
    public function execute(EventObserver $observer) // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    {
    }
}
