<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Klevu\Categorynavigation\Model\ResourceModel\Product;

use Magento\Framework\App\RequestInterface;

/**
 * Fulltext Collection
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection
{
	const KLEVU_PRESERVE_LAYOUT    = 2;
    const MAGENTO_DEFAULT     = 1;
    const KLEVU_TEMPLATE_LAYOUT = 3;
     /**
     * Klevu Search API Parameters
     * @var array
     */
    protected $_klevu_parameters;
    protected $_klevu_tracking_parameters;
    protected $_klevu_type_of_records = 'KLEVU_PRODUCT';
    /**
     * Klevu Search API Product IDs
     * @var array
     */
    protected $_klevu_product_ids = [];
    protected $_klevu_parent_child_ids = [];
    /**
     * Klevu Search API Response
     * @var \Klevu\Search\Model\Api\Response
     */
    protected $_klevu_response;
    /**
     * Search query
     * @var string
     */
    protected $_query;
    /**
     * Total number of results found
     * @var int
     */
    protected $_klevu_size;
    /**
     * The XML Response from Klevu
     * @var SimpleXMLElement
     */
    protected $_klevu_response_xml;
    /**
     * @var \Klevu\Search\Model\Api\Action\Idsearch
     */
    protected $_apiActionIdsearch;
    /**
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    
    protected $_session;
    /**
     * @param RequestInterface $request
     */
    public function __construct(
    	RequestInterface $request,
        \Klevu\Search\Helper\Config $searchHelperConfig,
        \Klevu\Search\Helper\Data $searchHelperData,
        \Klevu\Categorynavigation\Helper\Data $searchHelper,
        \Klevu\Categorynavigation\Model\Api\Action\Idsearch $apiActionIdsearch,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Klevu\Categorynavigation\Helper\Data $categorynavigationHelperConfig,
        \Magento\Framework\Session\Generic $session
    ){
        $this->request = $request;
        $this->_searchHelperConfig = $searchHelperConfig;
        $this->_searchHelperData = $searchHelperData;
        $this->_searchHelper = $searchHelper;
        $this->_apiActionIdsearch = $apiActionIdsearch;
        $this->_registry = $registry;
        $this->scopeConfig = $scopeConfig;
        $this->_categorynavigationHelperConfig = $categorynavigationHelperConfig;
        $this->_session = $session;
    }
    
   	public function afterSetOrder(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection)
    {
	  
		$helper = $this->_searchHelper;
        $config = $this->_searchHelperConfig;
        if ($config->isExtensionConfigured() && $helper->categoryLandingStatus() == static::KLEVU_PRESERVE_LAYOUT) {
        $currentCategory = $this->_registry->registry('current_category');
        $categoryId = $this->_session->getData('category_klevu_id');
            if($currentCategory)
            {
				$this->_session->setData('category_klevu_id', $currentCategory->getId());
				$this->_registry->register('klevu_product_ids',$this->_getProductIds());	
			}
			
            $collection_order = $this->request->getParam('product_list_order');
            $module_name = $this->request->getModuleName();
            $action = $this->request->getActionName();
            if (empty($collection_order) && !empty($this->_registry->registry('klevu_product_ids')) && $module_name == "catalog" && $action == "view") {
				$collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                $collection->getSelect()->order(new \Zend_Db_Expr(sprintf('FIELD(`e`.`entity_id`, %s) ASC', implode(',', $this->_registry->registry('klevu_product_ids')))));
            } else {
                if ($collection_order == 'relevance' && !empty($this->_registry->registry('klevu_product_ids')) && $module_name == "catalog" && $action == "view") {
				$collection->getSelect()->reset(\Zend_Db_Select::ORDER);
                $collection->getSelect()->order(new \Zend_Db_Expr(sprintf('FIELD(`e`.`entity_id`, %s) ASC', implode(',', $this->_registry->registry('klevu_product_ids')))));
                }
            }     
        }
        return $collection;
	}
    
    public function getSearchFilters()
    {
        
        $currentCategory = $this->_registry->registry('current_category');
        foreach ($currentCategory->getParentCategories() as $parent) {
    		$catnames[] = $parent->getName();
		}
		$allCategoryNames = implode(";",$catnames);
        $catnames = array();
        $pathIds = array();
        $pathIds = $currentCategory->getPathIds();
        if(!empty($pathIds)) {
            unset($pathIds[0]);
            unset($pathIds[1]);
            foreach ($pathIds as $key => $value){
                $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $catnames[] = $_objectManager->create('Magento\Catalog\Model\Category')
                    ->load($value)->getName();
            }
            $allCategoryNames = implode(";",$catnames);
        }


		$category = $this->_klevu_type_of_records." ".$allCategoryNames;
		if($this->_categorynavigationHelperConfig->getNoOfResults()){
			$noOfResults = $this->_categorynavigationHelperConfig->getNoOfResults();
		}else{
			$noOfResults = 2000;
		}
        if (empty($this->_klevu_parameters)) {
            $this->_klevu_parameters = [
                'ticket' => $this->_searchHelperConfig->getJsApiKey() ,
                'noOfResults' => $noOfResults,
                'term' => '*',
                'paginationStartsFrom' => 0,
                'enableFilters' => 'false',
                'klevuShowOutOfStockProducts' => 'true',
                'isCategoryNavigationRequest' => 'true',
                'category' => $category
            ];
        }
        return $this->_klevu_parameters;
    }	
	
    /**
     * Send the API Request and return the API Response.
     * @return \Klevu\Search\Model\Api\Response
     */
    public function getKlevuResponse()
    {
        if (!$this->_klevu_response) {
            $this->_klevu_response = $this->_apiActionIdsearch->execute($this->getSearchFilters());
        }
        return $this->_klevu_response;
    }

    /**
     * This method executes the the Klevu API request if it has not already been called, and takes the result
     * with the result we get all the item IDs, pass into our helper which returns the child and parent id's.
     * We then add all these values to our class variable $_klevu_product_ids.
     *
     * @return array
     */
    protected function _getProductIds()
    {

        if (empty($this->_klevu_product_ids)) {
            // If no results, return an empty array

            if (!$this->getKlevuResponse()->hasData('result')) {
                return [];
            }
			//print_r($this->getKlevuResponse()->getData('result')); exit;
            foreach ($this->getKlevuResponse()->getData('result') as $key => $result) {
                if (isset($result['id'])) {
                    $item_id = $this->_searchHelperData->getMagentoProductId((string)$result['id']);
                    $this->_klevu_parent_child_ids[] = $item_id;
                    if ($item_id['parent_id'] != 0) {
                        $this->_klevu_product_ids[$item_id['parent_id']] = $item_id['parent_id'];
                    } else {
                        $this->_klevu_product_ids[$item_id['product_id']] = $item_id['product_id'];
                    }
                } else {
                    if ($key == "id") {
                        $item_id = $this->_searchHelperData->getMagentoProductId((string)$result);
                        $this->_klevu_parent_child_ids[] = $item_id;
                        if ($item_id['parent_id'] != 0) {
                            $this->_klevu_product_ids[$item_id['parent_id']] = $item_id['parent_id'];
                        } else {
                            $this->_klevu_product_ids[$item_id['product_id']] = $item_id['product_id'];
                        }
                    }
                }
            }

            $this->_klevu_product_ids = array_unique($this->_klevu_product_ids);
            $this->_searchHelperData->log(\Zend\Log\Logger::DEBUG, sprintf("Products count returned: %s", count($this->_klevu_product_ids)));
        }
        return $this->_klevu_product_ids;
    }
}
