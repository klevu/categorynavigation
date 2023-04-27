<?php

namespace Klevu\Categorynavigation\Plugin\Framework\Search\Request;

use Klevu\Categorynavigation\Helper\Data as KlevuHelperDataCatNav;
use Klevu\Categorynavigation\Model\Api\Magento\Request\CategoryInterface as KlevuCategoryApi;
use Klevu\Logger\Constants as LoggerConstants;
use Klevu\Search\Helper\Config as KlevuHelperConfig;
use Klevu\Search\Helper\Data as KlevuHelperData;
use Magento\Catalog\Model\Session;
use Magento\Catalog\Model\SessionFactory;
use Magento\Framework\Registry as MagentoRegistry;
use Magento\Framework\Search\Request\Cleaner as MagentoCleaner;
use Magento\Framework\Session\SessionManagerInterface as MageSessionManager;
use Magento\PageCache\Model\Config as MagentoPageCache;

class CleanerPluginForCatNav
{
    const KLEVU_PRESERVE_LAYOUT = 2;
    const MAGENTO_DEFAULT = 1;
    const KLEVU_TEMPLATE_LAYOUT = 3;

    /**
     * @var Session|MageSessionManager
     */
    protected $sessionObjectHandler;
    /**
     * @var MagentoRegistry
     */
    protected $magentoRegistry;
    /**
     * @var MagentoCleaner
     */
    protected $magentoCleaner;
    /**
     * @var KlevuCategoryApi
     */
    protected $klevuCategoryRequest;
    /**
     * @var KlevuHelperData
     */
    protected $klevuHelperData;
    /**
     * @var KlevuHelperConfig
     */
    protected $klevuHelperConfig;
    /**
     * @var KlevuHelperDataCatNav
     */
    protected $klevuHelperDataCatNav;
    /**
     * @var bool
     */
    protected $isKlevuPreserveLogEnabled = false;
    /**
     * @var SessionFactory
     */
    protected $sessionFactory;

    /**
     * CleanerPluginForCatNav constructor.
     *
     * @param MagentoRegistry $mageRegistry
     * @param MagentoCleaner $mageCleaner
     * @param MagentoPageCache $magePageCache
     * @param MageSessionManager $sessionManager
     * @param SessionFactory $sessionFactory
     * @param KlevuCategoryApi $klevuCategoryRequest
     * @param KlevuHelperData $klevuHelperData
     * @param KlevuHelperConfig $klevuHelperConfig
     * @param KlevuHelperDataCatNav $klevuHelperDataCatNav
     */
    public function __construct(
        MagentoRegistry $mageRegistry,
        MagentoCleaner $mageCleaner,
        MagentoPageCache $magePageCache,
        MageSessionManager $sessionManager,
        SessionFactory $sessionFactory,
        KlevuCategoryApi $klevuCategoryRequest,
        KlevuHelperData $klevuHelperData,
        KlevuHelperConfig $klevuHelperConfig,
        KlevuHelperDataCatNav $klevuHelperDataCatNav
    ) {
        $this->magentoRegistry = $mageRegistry;
        $this->magentoCleaner = $mageCleaner;
        $this->sessionFactory = $sessionFactory;
        $this->klevuCategoryRequest = $klevuCategoryRequest;
        $this->klevuHelperData = $klevuHelperData;
        $this->klevuHelperConfig = $klevuHelperConfig;
        $this->klevuHelperDataCatNav = $klevuHelperDataCatNav;

        if ($magePageCache->isEnabled()) {
            $this->sessionObjectHandler = $this->sessionFactory->create();
        } else {
            $this->sessionObjectHandler = $sessionManager;
        }
    }

    /**
     * @param MagentoCleaner $subject
     * @param array $result
     *
     * @return array
     */
    public function afterClean(MagentoCleaner $subject, $result)
    {
        try {
            //Check if query is for catalog_view_container ( category view page )
            if (!isset($result['queries']['catalog_view_container'])) {
                return $result;
            }

            //Return if PRESERVE Layout is not enabled or module is not configured
            if (!$this->klevuHelperConfig->isExtensionConfigured()
                || (int)$this->klevuHelperDataCatNav->categoryLandingStatus() !== static::KLEVU_PRESERVE_LAYOUT
            ) {
                return $result;
            }
            $this->isKlevuPreserveLogEnabled = $this->klevuHelperConfig->isPreserveLayoutLogEnabled();

            $result = $this->klevuQueryCleanupCategory($result);
        } catch (\Exception $e) {
            $this->klevuHelperData->log(
                LoggerConstants::ZEND_LOG_CRIT,
                sprintf("Klevu_CatNav_Cleaner::Cleaner ERROR occured :%s", $e->getMessage())
            );
        }

        return $result;
    }

    /**
     * Klevu Cleanup for Category Navigation
     *
     * @param array $requestData
     *
     * @return array
     */
    public function klevuQueryCleanupCategory($requestData)
    {
        $catValue = $requestData['filters']['category_filter']['value'];
        if ($this->isKlevuPreserveLogEnabled) {
            $this->writeToPreserveLayoutLog("catNavCleanerPlugin:: klevuQueryCleanupCategory execution started");
            //Adding this to identify in the logs which cleaner is triggering
            $this->magentoRegistry->unregister('klReqCleanerType');
            $this->magentoRegistry->register('klReqCleanerType', 'CategoryNavRequestInitiated');
        }

        //If multiple category paths requested then return the $requestData
        if (is_array($catValue) && count($catValue) > 1) {
            $this->klevuHelperData->log(
                LoggerConstants::ZEND_LOG_DEBUG,
                sprintf("Request has multiple category filter values %s", implode(",", $catValue))
            );

            return $requestData;
        }
        if (is_array($catValue) && count($catValue) === 1) {
            $catValue = $catValue[0];
        }

        $queryScope = $requestData['dimensions']['scope']['value'];

        $idList = $this->sessionObjectHandler->getData('ids_' . $queryScope . '_cat_' . $catValue);
        if (!$idList) {
            $idList = $this->klevuCategoryRequest->_getKlevuProductIds();
            if (empty($idList)) {
                $idList = [0];
            } //to handle mysql blank IN()
            $this->sessionObjectHandler->setData('ids_' . $queryScope . '_cat_' . $catValue, $idList);
        }

        //register the id list so it will be used when ordering
        $this->magentoRegistry->unregister('search_ids');
        $this->magentoRegistry->register('search_ids', $idList);

        //To get the Variant Selection for Category Pages if Preserve Layout Option
        $parentChildIDs = $this->sessionObjectHandler->getData(
            'parentChildIDs_' . $queryScope . '_cat_' . $catValue
        );
        if (!$parentChildIDs) {
            $parentChildIDs = $this->klevuCategoryRequest->getKlevuVariantParentChildIds();
            if (empty($parentChildIDs)) {
                $parentChildIDs = [];
            }
            $this->sessionObjectHandler->setData(
                'parentChildIDs_' . $queryScope . '_cat_' . $catValue,
                $parentChildIDs
            );
        }
        //register the parentChildIDs
        $this->magentoRegistry->unregister('parentChildIDsCatNav');
        $this->magentoRegistry->register('parentChildIDsCatNav', $parentChildIDs);

        //Checking for queryReference
        if (empty($requestData['queries']['catalog_view_container']['queryReference'])) {
            $this->klevuHelperData->log(
                LoggerConstants::ZEND_LOG_DEBUG,
                "catNavCleanerPlugin:: queryReference not found"
            );

            return $requestData;
        }
        $excludeIds = $this->sessionObjectHandler->getData('exclIds_' . $queryScope . '_cat_' . $catValue);
        if (!$excludeIds) {
            //Reading ids from response
            $excludeIds = $this->klevuCategoryRequest->getKlevuProductExcludedIds();
            if (empty($excludeIds)) {
                $excludeIds = [];
            }
            $this->sessionObjectHandler->setData('exclIds_' . $queryScope . '_cat_' . $catValue, $excludeIds);
        }
        $this->magentoRegistry->unregister('klevu_exclude_ids');
        $this->magentoRegistry->register('klevu_exclude_ids', $excludeIds);

        if (!empty($excludeIds)) {
            $requestData['queries']['catalog_view_container']['queryReference'][] = [
                'clause' => 'not',
                'ref' => 'klevu_excl_ids',
            ];

            $requestData['queries']['klevu_excl_ids'] = [
                'name' => 'klevu_excl_ids',
                'filterReference' => [
                    [
                        'clause' => 'not',
                        'ref' => 'pid',
                    ],
                ],
                'type' => 'filteredQuery',
            ];
            $requestData['filters']['pid'] = [
                'name' => 'pid',
                'field' => $this->getProductIdField(),
                'value' => $excludeIds,
                'type' => 'termFilter',
                'is_bind' => 1,
            ];
        }

        $currentEngine = $this->getCurrentSearchEngine();
        if ($this->isKlevuPreserveLogEnabled) {
            $this->writeToPreserveLayoutLog(
                sprintf("catNavCleanerPlugin:: currentEngine-%s", $currentEngine)
            );
        }
        if ($currentEngine !== "mysql") {
            if (isset($requestData['sort'])) {
                if (count($requestData['sort']) > 0) {
                    foreach ($requestData['sort'] as $key => $value) {
                        if ($value['field'] === "personalized") {
                            $this->magentoRegistry->unregister('current_order');
                            $this->magentoRegistry->register('current_order', "personalized");
                        }
                    }
                }
            }

            $current_order = $this->magentoRegistry->registry('current_order');
            if ($this->isKlevuPreserveLogEnabled) {
                $this->writeToPreserveLayoutLog(
                    sprintf("catNavCleanerPlugin:: current_order-%s", $current_order)
                );
            }
            if (!empty($current_order)) {
                if ($current_order === "personalized") {
                    $this->magentoRegistry->unregister('from');
                    $this->magentoRegistry->unregister('size');
                    $this->magentoRegistry->register('from', $requestData['from']);
                    $this->magentoRegistry->register('size', $requestData['size']);
                    $requestData['from'] = 0;
                    $requestData['size'] = 4000;
                    $requestData['sort'] = [];
                }
            }
        }

        if ($this->isKlevuPreserveLogEnabled) {
            //convert requestData object into array
            $requestDataToArray = json_decode(json_encode($requestData), true);
            $this->writeToPreserveLayoutLog(
                // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                "catNavCleanerPlugin:: Request data:" . PHP_EOL . print_r($requestDataToArray, true)
            );
        }

        return $requestData;
    }

    /**
     * Writes logs to the Klevu_Search_Preserve_Layout.log file
     *
     * @param string $message
     *
     * @return void
     */
    private function writeToPreserveLayoutLog($message)
    {
        $this->klevuHelperData->preserveLayoutLog($message);
    }

    /**
     * Return current catalog search engine
     *
     * @return string
     */
    private function getCurrentSearchEngine()
    {
        return $this->klevuHelperConfig->getCurrentEngine();
    }

    /**
     * Return the product id field
     *
     * @return string
     */
    private function getProductIdField()
    {
        $currentEngine = $this->getCurrentSearchEngine();
        if (strpos($currentEngine, 'elasticsearch') !== false) {
            $currentEngine = "elasticsearch";
        }
        switch ($currentEngine) {
            case "elasticsearch5":
            case "elasticsearch6":
            case "elasticsearch7":
            case "solr":
            case "elasticsearch":
            case 'opensearch':
                $return = '_id';
                break;
            default:
                $return = 'entity_id';
                break;
        }

        return $return;
    }
}
