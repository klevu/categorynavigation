<?php

namespace Klevu\Categorynavigation\Model\Observer;

use Klevu\Categorynavigation\Helper\Config as CategoryNavigationConfigHelper;
use Klevu\Categorynavigation\Helper\Data as CategoryNavigationHelper;
use Klevu\Categorynavigation\Model\Api\Action\CategoryNavigationUrl as CategoryNavigationUrlAction;
use Klevu\Search\Helper\Config as SearchConfigHelper;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class CateoryNavigationUrl implements ObserverInterface
{
    /**
     * @var CategoryNavigationHelper
     */
    protected $_categorynavigationHelper;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var CategoryNavigationUrlAction
     */
    protected $_apiActionCategoryNavigationURL;
    /**
     * @var SearchConfigHelper
     */
    protected $_searchHelperConfig;
    /**
     * @var CategoryNavigationConfigHelper
     */
    protected $_categorynavigationHelperConfig;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ConfigInterface
     */
    protected $_configInterface;

    /**
     * @param CategoryNavigationHelper $categorynavigationHelper
     * @param SearchConfigHelper $searchHelperConfig
     * @param CategoryNavigationConfigHelper $categorynavigationHelperConfig
     * @param Request $request
     * @param CategoryNavigationUrlAction $apiActionCategoryNavigationURL
     * @param ConfigInterface $configInterface
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        CategoryNavigationHelper $categorynavigationHelper,
        SearchConfigHelper $searchHelperConfig,
        CategoryNavigationConfigHelper $categorynavigationHelperConfig,
        Request $request,
        CategoryNavigationUrlAction $apiActionCategoryNavigationURL,
        ConfigInterface $configInterface,
        LoggerInterface $logger = null
    ) {
        $this->_categorynavigationHelper = $categorynavigationHelper;
        $this->request = $request;
        $this->_apiActionCategoryNavigationURL = $apiActionCategoryNavigationURL;
        $this->_searchHelperConfig = $searchHelperConfig;
        $this->_categorynavigationHelperConfig = $categorynavigationHelperConfig;
        $this->_configInterface = $configInterface;
        $this->logger = $logger
            ?: ObjectManager::getInstance()->get(LoggerInterface::class);
    }

    /**
     * If Cat Nav feature enabled then Cat Nav URL value will be saved.
     *
     * @param Observer $observer
     *
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $store = $this->request->getParam("store");
        if (null === $store) {
            return;
        }

        $config_state = $this->request->getParam('groups');
        if (!isset($config_state['general']['fields']['category_navigation_url']['value'])) {
            return;
        }

        $valueCategoryLanding = $config_state['general']['fields']['category_navigation_url']['value'];
        if ((string)$this->_categorynavigationHelper->getCategoryNavigationUrl() === (string)$valueCategoryLanding) {
            return;
        }

        $restApi = $this->_searchHelperConfig->getRestApiKey($store);
        $param = [
            "restApiKey" => $restApi,
            "store" => $store,
        ];
        $response = $this->_apiActionCategoryNavigationURL->execute($param);
        if (!$response->isSuccess()) {
            $this->logger->error(
                'Encountered error while retrieving category navigation endpoints',
                [
                    'message' => $response->getMessage(),
                    'error' => $response->getDataUsingMethod('error'),
                ]
            );

            return;
        }

        $category_navigation_url = $response->getCategoryNavigationUrl();
        if ($category_navigation_url) {
            $this->_categorynavigationHelperConfig->setCategoryNavigationUrl(
                $category_navigation_url,
                $store
            );
        }

        $category_navigation_tracking_url = $response->getDataUsingMethod('stats_url');
        if ($category_navigation_tracking_url) {
            $this->_categorynavigationHelperConfig->setCategoryNavigationTrackingUrl(
                $category_navigation_tracking_url,
                $store
            );
        }
    }
}
