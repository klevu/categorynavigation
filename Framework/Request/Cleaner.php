<?php
/**
 * Klevu override of the request Cleaner for use on preserve layout
 */
namespace Klevu\Categorynavigation\Framework\Request;

use Klevu\Search\Framework\Request\Cleaner as KlevuCoreCleaner;
use Klevu\Search\Model\Context as KlevuCoreContext;

class Cleaner extends KlevuCoreCleaner
{

    const KLEVU_PRESERVE_LAYOUT    = 2;
    const MAGENTO_DEFAULT     = 1;
    const KLEVU_TEMPLATE_LAYOUT = 3;



    /**
     * Clean not binder queries and filters
     *
     * @param array $requestData
     * @return array
     */
    public function clean(array $requestData)
    {
        $requestData = parent::clean($requestData);
        $requestData = $this->klevuQueryCleanupCategory($requestData);
        return $requestData;
    }




    /**
     * @param $requestData array
     * @return array
     */
    public function klevuQueryCleanupCategory($requestData){

        // check if we are in search page
        if(!isset($requestData['queries']['catalog_view_container'])) return $requestData;


        //check if klevu is supposed to be on

        $helper = $this->klevuCoreContext->getHelperManager()->getCatnavDataHelper();

        $config = $this->klevuConfig;
        if (!$helper && !$config->isExtensionConfigured() || $helper->categoryLandingStatus() != static::KLEVU_PRESERVE_LAYOUT) return $requestData;

        $apiEndpoint = $this->klevuCoreContext->getKlevuContextApi()->getKlevuCoreApi()->getKlevuCatnavId();

        if(!$apiEndpoint) return $requestData;
        $catValue = $requestData['filters']['category_filter']['value'];
        $queryScope = $requestData['dimensions']['scope']['value'];
        $idList = $this->sessionManager->getData('ids_'.$queryScope.'_cat_'.$catValue);
        if(!$idList){
            $idList = $apiEndpoint->_getKlevuProductIds();
            if(empty($idList)) $idList = array(0);
            $this->sessionManager->setData('ids_'.$queryScope.'_cat_'.$catValue,$idList );
        }
        //register the id list so it will be used when ordering
        $this->magentoRegistry->unregister('search_ids');
        $this->magentoRegistry->register('search_ids', $idList);

        $currentEngine = $this->klevuConfig->getCurrentEngine();
        if( $currentEngine !== "mysql") {
            if (isset($requestData['sort'])) {
                if (count($requestData['sort']) > 0) {
                    foreach ($requestData['sort'] as $key => $value) {
                        if ($value['field'] == "personalized") {
                            $this->magentoRegistry->register('current_order', "personalized");
                        }

                    }
                }
            }

            $current_order = $this->magentoRegistry->registry('current_order');
            if (!empty($current_order)) {
                if ($current_order == "personalized") {
                    $this->magentoRegistry->register('from', $requestData['from']);
                    $this->magentoRegistry->register('size', $requestData['size']);
                    $requestData['from'] = 0;
                    $requestData['size'] = count($idList);
                    $requestData['sort'] = array();
                }
            }
        }
        return $requestData;
    }
}