<?php
/**
 * Klevu Product API model for preserve layout
 */
namespace Klevu\Categorynavigation\Model\Api\Magento\Request;

use Klevu\Search\Helper\Config as KlevuHelperConfig;
use Klevu\Search\Helper\Data as KlevuHelperData;
use Klevu\Categorynavigation\Model\Api\Action\CatnavIdsearch as KlevuCatnavApiIdsearch;
use Klevu\Search\Model\Api\Action\Searchtermtracking as KlevuApiSearchtermtracking;
use \Magento\Framework\App\Request\Http as Magento_Request;
use \Magento\Catalog\Model\CategoryFactory as Magento_CategoryFactory;
use \Magento\Catalog\Model\Category as Category_Model;
use Klevu\Categorynavigation\Helper\Data as KlevuCatNavHelperData;
use Zend\Log\Logger as Logger;

class Category implements CategoryInterface
{

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
     * @var \Klevu\Search\Helper\Config
     */
    protected $_searchHelperConfig;
    /**
     * @var \Klevu\Search\Helper\Data
     */
    protected $_searchHelperData;
    /**
     * @var \Klevu\Search\Model\Api\Action\Idsearch
     */
    protected $_apiActionIdsearch;
    /**
     * @var \Klevu\Search\Model\Api\Action\Searchtermtracking
     */
    protected $_apiActionSearchtermtracking;

    public function __construct(
        KlevuHelperConfig $searchHelperConfig,
        KlevuCatNavHelperData $categorynavigationHelperConfig,
        KlevuHelperData $searchHelperData,
        KlevuCatnavApiIdsearch $apiActionIdsearch,
        KlevuApiSearchtermtracking $apiActionSearchtermtracking,
        Magento_Request $magentoRequest,
        Magento_CategoryFactory $magentoCategoryFactory,
        \Magento\Framework\Registry $registry,
        Category_Model $categoryModel

    )
    {
        $this->_searchHelperConfig = $searchHelperConfig;
        $this->_searchHelperData = $searchHelperData;
        $this->_apiActionIdsearch = $apiActionIdsearch;
        $this->_apiActionSearchtermtracking = $apiActionSearchtermtracking;
        $this->_magentoRequest = $magentoRequest;
        $this->_magentoCategoryFactory = $magentoCategoryFactory;
        $this->_categoryModel = $categoryModel;
        $this->_registry = $registry;
        $this->_categorynavigationHelperConfig = $categorynavigationHelperConfig;
    }

    /**
     * This method executes the the Klevu API request if it has not already been called, and takes the result
     * with the result we get all the item IDs, pass into our helper which returns the child and parent id's.
     * We then add all these values to our class variable $_klevu_product_ids.
     *
     * @return array
     */
    public function _getKlevuProductIds()
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
			$this->_klevu_product_ids = array_values($this->_klevu_product_ids);            
            $this->_searchHelperData->log(\Zend\Log\Logger::DEBUG, sprintf("Products count returned: %s", count($this->_klevu_product_ids)));
        }
        return $this->_klevu_product_ids;
    }

    /**
     * Send the API Request and return the API Response.
     * @param $query
     * @return \Klevu\Search\Model\Api\Response
     */
    private function getKlevuResponse()
    {
        if (!$this->_klevu_response) {
            $this->_klevu_response = $this->_apiActionIdsearch->execute($this->getSearchFilters());
        }
        return $this->_klevu_response;
    }

    /**
     * Return the Klevu api search filters
     * @param $query
     * @return array
     */
    public function getSearchFilters()
    {
		$catnames = array();
		$parentnames = array();
		try{
			$currentCategory = $this->_registry->registry('current_category');
			if(!$currentCategory instanceof Category_Model) {
				return false;
			}
			foreach ($currentCategory->getParentCategories() as $parent) {
				$parentnames[] = $parent->getName();
			}
			$allCategoryNames = implode(";",$parentnames);
			
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
					'category' => $category,
					'categoryIds' => $currentCategory->getId(),
					'visibility' => 'catalog'
				];
			}
			return $this->_klevu_parameters;
		} catch (\Exception $e) {
            $this->_searchHelperData->log(\Zend\Log\Logger::CRIT, sprintf("Exception thrown in %s::%s - %s", __CLASS__, __METHOD__, $e->getMessage()));
        }   
    }


    /**
     * This method resets the saved $_klevu_product_ids.
     * @return boolean
     */
    public function reset()
    {
        $this->_klevu_product_ids = null;
        return true;
    }
}