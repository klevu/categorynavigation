<?php

namespace Klevu\Categorynavigation\Observer\Backend;

use Klevu\Categorynavigation\Helper\Config as Klevu_HelperConfigCatNav;
use Klevu\Logger\Constants as LoggerConstants;
use Klevu\Search\Model\Klevu\HelperManager as Klevu_HelperManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;

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
     */
    public function execute(EventObserver $observer)
    {
        try {
            if (!($this->_storeManager->isSingleStoreMode())) {
                return;
            }
            if (
                $this->_request->getFullActionName() !== 'adminhtml_system_config_edit' ||
                $this->_request->getParam('section') !== 'klevu_search'
            ) {
                return;
            }
            /** @var \Klevu\Search\Helper\Config $klevuConfig */
            $klevuConfig = $this->_klevuHelperManager->getConfigHelper();
            if (!$klevuConfig->getModuleInfoCatNav()) {
                return;
            }
            $catNavURL = $this->_klevuHelperConfigCatNav->getCategoryNavigationUrl($this->_storeManager->getStore());
            $catNavTrackURL = $this->_klevuHelperConfigCatNav->getCategoryNavigationTrackingUrl($this->_storeManager->getStore());

            $klevuConfig->setGlobalConfig(Klevu_HelperConfigCatNav::XML_PATH_CATEGORY_NAVIGATION_URL, $catNavURL);
            $klevuConfig->setGlobalConfig(Klevu_HelperConfigCatNav::XML_PATH_CATEGORY_NAVIGATION_TRACKING_URL, $catNavTrackURL);
        } catch (\Exception $e) {
            $klevuDataHelper = $this->_klevuHelperManager->getDataHelper();
            $klevuDataHelper->log(
                LoggerConstants::ZEND_LOG_CRIT,
                sprintf(
                    "Exception thrown for single store view cat nav %s::%s - %s",
                    __CLASS__, __METHOD__, $e->getMessage()
                )
            );
        }
    }

}

