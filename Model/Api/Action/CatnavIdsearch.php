<?php

namespace Klevu\Categorynavigation\Model\Api\Action;

class CatnavIdsearch extends \Klevu\Search\Model\Api\Actionall
{
    /**
     * @var \Klevu\Search\Model\Api\Response\Invalid
     */
    protected $_apiResponseInvalid;

    /**
     * @var \Klevu\Search\Helper\Api
     */
    protected $_searchHelperApi;

    /**
     * @var \Klevu\Search\Helper\Config
     */
    protected $_searchHelperConfig;
    
     /**
      * @var \Magento\Store\Model\StoreManagerInterface
      */
    protected $_storeModelStoreManagerInterface;

    public function __construct(
        \Klevu\Search\Model\Api\Response\Invalid $apiResponseInvalid,
        \Klevu\Search\Helper\Api $searchHelperApi,
        \Magento\Store\Model\StoreManagerInterface $storeModelStoreManagerInterface,
        \Klevu\Search\Helper\Config $searchHelperConfig,
		\Klevu\Categorynavigation\Helper\Config $categorynavigationHelperConfig
    ) {
    
        $this->_apiResponseInvalid = $apiResponseInvalid;
        $this->_searchHelperApi = $searchHelperApi;
        $this->_searchHelperConfig = $searchHelperConfig;
        $this->_storeModelStoreManagerInterface = $storeModelStoreManagerInterface;
		$this->_categorynavigationHelperConfig = $categorynavigationHelperConfig;
    }

    const ENDPOINT = "/cloud-search/n-search/idsearch";
    const METHOD   = "GET";

    const DEFAULT_REQUEST_MODEL = "Klevu\Search\Model\Api\Request\Get";
    const DEFAULT_RESPONSE_MODEL = "Klevu\Search\Model\Api\Response\Data";
    
    /**
     * Get the store used for this request.
     * @return \Magento\Framework\Model\Store
     */
    public function getStore()
    {
        if (!$this->hasData('store')) {
            $this->setData('store', $this->_storeModelStoreManagerInterface->getStore());
        }

        return $this->getData('store');
    }
    
    protected function validate($parameters)
    {
        $errors = [];

        if (!isset($parameters['ticket']) || empty($parameters['ticket'])) {
            $errors['ticket'] = "Missing ticket (Search API Key)";
        }

        if (!isset($parameters['noOfResults']) || empty($parameters['noOfResults'])) {
            $errors['noOfResults'] = "Missing number of results to return";
        }

        if (!isset($parameters['term']) || empty($parameters['term'])) {
            $errors['term'] = "Missing search term";
        }

        if (!isset($parameters['paginationStartsFrom'])) {
            $errors['paginationStartsFrom'] = "Missing pagination start from value ";
        } elseif ((int)$parameters['paginationStartsFrom'] < 0) {
            $errors['paginationStartsFrom'] = "Pagination needs to start from 0 or higher";
        }

        if (count($errors) == 0) {
            return true;
        }
        return $errors;
    }

    /**
     * Execute the API action with the given parameters.
     *
     * @param array $parameters
     *
     * @return \Klevu\Search\Model\Api\Response
     */
    public function execute($parameters = [])
    {

        $validation_result = $this->validate($parameters);
        if ($validation_result !== true) {
            return $this->_apiResponseInvalid->setErrors($validation_result);
        }
        
        $request = $this->getRequest();

        $endpoint = $this->buildEndpoint(
            static::ENDPOINT,
            $this->getStore(),
            $this->_categorynavigationHelperConfig->getCategoryNavigationUrl($this->getStore())
        );
        
        $request
            ->setResponseModel($this->getResponse())
            ->setEndpoint($endpoint)
            ->setMethod(static::METHOD)
            ->setData($parameters);

        return $request->send();
    }
    
    public function buildEndpoint($endpoint, $store = null, $hostname = null)
    {
        return static::ENDPOINT_PROTOCOL . (($hostname) ? $hostname : $this->_searchHelperConfig->getHostname($store)) . $endpoint;
    }
}
