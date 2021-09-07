<?php
namespace Klevu\Categorynavigation\Observer\Backend;

use Klevu\Categorynavigation\Helper\Config as Klevu_HelperConfigCatNav;
use Klevu\Logger\Constants as LoggerConstants;
use Klevu\Search\Model\Klevu\HelperManager as Klevu_HelperManager;
use Magento\Framework\App\RequestInterface as RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManagerInterface;

class SingleStoreViewConfigToShowForCatNav implements ObserverInterface
{
    private $_request;

    private $_storeManager;

    private $_klevuHelperConfig;

	
	private $_klevuHelperManager;


    private $_klevuHelperConfigCatNav;

    public function __construct(
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        Klevu_HelperManager $klevuHelperManager,
        Klevu_HelperConfigCatNav $KlevuHelperConfigCatNav
    )
    {
        $this->_request = $request;
        $this->_storeManager = $storeManager;
        $this->_klevuHelperManager = $klevuHelperManager;
        $this->_klevuHelperConfigCatNav = $KlevuHelperConfigCatNav;
    }

    public function execute(EventObserver $observer)
    {

		$klevuDataHelper = $this->_klevuHelperManager->getDataHelper();

        try{
            $isSingleStoreMode = $this->_storeManager->isSingleStoreMode();
            $klevuConfigCatNav = $this->_klevuHelperConfigCatNav;
            $klevuConfig = $this->_klevuHelperManager->getConfigHelper();

            $actionFlag = FALSE;
            if( $this->_request->getFullActionName() == 'adminhtml_system_config_edit' &&
                $this->_request->getParam('section') == 'klevu_search' ) {
                $actionFlag = TRUE;
            }
            if(!$isSingleStoreMode || !$klevuConfig->getModuleInfoCatNav() || !$actionFlag) {
                return;
            }

            $catNavURL = $klevuConfigCatNav->getCategoryNavigationUrl( $this->_storeManager->getStore() );
            $catNavTrackURL = $klevuConfigCatNav->getCategoryNavigationTrackingUrl( $this->_storeManager->getStore() );

            $klevuConfig->setGlobalConfig( $klevuConfigCatNav::XML_PATH_CATEGORY_NAVIGATION_URL , $catNavURL );
            $klevuConfig->setGlobalConfig( $klevuConfigCatNav::XML_PATH_CATEGORY_NAVIGATION_TRACKING_URL , $catNavTrackURL );

        } catch (\Exception $e) {
            $klevuDataHelper->log(LoggerConstants::ZEND_LOG_CRIT, sprintf("Exception thrown for single store view cat nav %s::%s - %s", __CLASS__, __METHOD__, $e->getMessage()));
            return;
        }
    }

}

