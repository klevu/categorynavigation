<?php

namespace Klevu\Categorynavigation\Model\Api\Action;

use Klevu\Categorynavigation\Helper\Config as CatNavConfigHelper;
use Klevu\Search\Helper\Api as ApiHelper;
use Klevu\Search\Helper\Config as SearchConfigHelper;
use Klevu\Search\Model\Api\Actionall;
use Klevu\Search\Model\Api\Request\Get as ApiGetRequest;
use Klevu\Search\Model\Api\Response;
use Klevu\Search\Model\Api\Response\Data as ApiResponseData;
use Klevu\Search\Model\Api\Response\Invalid as ResponseInvalid;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class CatnavIdsearch extends Actionall
{
    const ENDPOINT = "/cloud-search/n-search/idsearch";
    const METHOD   = "GET";
    const DEFAULT_REQUEST_MODEL = ApiGetRequest::class;
    const DEFAULT_RESPONSE_MODEL = ApiResponseData::class;

    /**
     * @var ResponseInvalid
     */
    protected $_apiResponseInvalid;
    /**
     * @var ApiHelper
     */
    protected $_searchHelperApi;
    /**
     * @var SearchConfigHelper
     */
    protected $_searchHelperConfig;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeModelStoreManagerInterface;

    /**
     * @param ResponseInvalid $apiResponseInvalid
     * @param ApiHelper $searchHelperApi
     * @param StoreManagerInterface $storeModelStoreManagerInterface
     * @param SearchConfigHelper $searchHelperConfig
     * @param CatNavConfigHelper $categorynavigationHelperConfig
     * @param string|null $requestModel
     * @param string|null $responseModel
     */
    public function __construct(
        ResponseInvalid $apiResponseInvalid,
        ApiHelper $searchHelperApi,
        StoreManagerInterface $storeModelStoreManagerInterface,
        SearchConfigHelper $searchHelperConfig,
        CatNavConfigHelper $categorynavigationHelperConfig,
        $requestModel = null,
        $responseModel = null
    ) {
        parent::__construct(
            $apiResponseInvalid,
            $searchHelperConfig,
            $storeModelStoreManagerInterface,
            $requestModel ?: static::DEFAULT_REQUEST_MODEL,
            $responseModel ?: static::DEFAULT_RESPONSE_MODEL
        );

        $this->_apiResponseInvalid = $apiResponseInvalid;
        $this->_searchHelperApi = $searchHelperApi;
        $this->_searchHelperConfig = $searchHelperConfig;
        $this->_storeModelStoreManagerInterface = $storeModelStoreManagerInterface;
        $this->_categorynavigationHelperConfig = $categorynavigationHelperConfig;
    }

    /**
     * Get the store used for this request.
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore()
    {
        if (!$this->hasData('store')) {
            $this->setData('store', $this->_storeModelStoreManagerInterface->getStore());
        }

        return $this->getData('store');
    }

    /**
     * @param array $parameters
     *
     * @return array|true
     */
    protected function validate($parameters)
    {
        $errors = [];
        if (empty($parameters['ticket'])) {
            $errors['ticket'] = "Missing ticket (Search API Key)";
        }
        if (empty($parameters['noOfResults'])) {
            $errors['noOfResults'] = "Missing number of results to return";
        }
        if (empty($parameters['term'])) {
            $errors['term'] = "Missing search term";
        }
        if (!isset($parameters['paginationStartsFrom'])) {
            $errors['paginationStartsFrom'] = "Missing pagination start from value ";
        } elseif ((int)$parameters['paginationStartsFrom'] < 0) {
            $errors['paginationStartsFrom'] = "Pagination needs to start from 0 or higher";
        }
        if (!count($errors)) {
            return true;
        }

        return $errors;
    }

    /**
     * Execute the API action with the given parameters.
     *
     * @param array $parameters
     *
     * @return Response
     * @throws NoSuchEntityException
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

    /**
     * @param string $endpoint
     * @param StoreInterface|string|int|null $store
     * @param string $hostname
     *
     * @return string
     */
    public function buildEndpoint($endpoint, $store = null, $hostname = null)
    {
        return static::ENDPOINT_PROTOCOL
            . ($hostname ?: $this->_searchHelperConfig->getHostname($store))
            . $endpoint;
    }
}
