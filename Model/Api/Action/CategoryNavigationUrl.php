<?php

namespace Klevu\Categorynavigation\Model\Api\Action;

use Klevu\Search\Helper\Api as ApiHelper;
use Klevu\Search\Helper\Config as ConfigHelper;
use Klevu\Search\Helper\Data as SearchHelper;
use Klevu\Search\Model\Api\Actionall;
use Klevu\Search\Model\Api\Request\Post as ApiPostRequest;
use Klevu\Search\Model\Api\Response;
use Klevu\Search\Model\Api\Response\Data as ApiResponseData;
use Klevu\Search\Model\Api\Response\Invalid as InvalidResponse;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class CategoryNavigationUrl extends Actionall
{
    const ENDPOINT = "/n-search/getCategoryNavigationURL";
    const METHOD   = "POST";
    const DEFAULT_REQUEST_MODEL = ApiPostRequest::class;
    const DEFAULT_RESPONSE_MODEL = ApiResponseData::class;

    /**
     * @var InvalidResponse
     */
    protected $_apiResponseInvalid;
    /**
     * @var Store
     */
    protected $_frameworkModelStore;
    /**
     * @var ApiHelper
     */
    protected $_searchHelperApi;
    /**
     * @var ConfigHelper
     */
    protected $_searchHelperConfig;
    /**
     * @var SearchHelper
     */
    protected $_searchHelperData;

    /**
     * @param InvalidResponse $apiResponseInvalid
     * @param ApiHelper $searchHelperApi
     * @param ConfigHelper $searchHelperConfig
     * @param StoreManagerInterface $storeModelStoreManagerInterface
     * @param SearchHelper $searchHelperData
     * @param Store $frameworkModelStore
     * @param string|null $requestModel
     * @param string|null $responseModel
     */
    public function __construct(
        InvalidResponse $apiResponseInvalid,
        ApiHelper $searchHelperApi,
        ConfigHelper $searchHelperConfig,
        StoreManagerInterface $storeModelStoreManagerInterface,
        SearchHelper $searchHelperData,
        Store $frameworkModelStore,
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
        $this->_searchHelperData = $searchHelperData;
        $this->_frameworkModelStore = $frameworkModelStore;
    }

    /**
     * @param array $parameters
     *
     * @return array|true
     */
    protected function validate($parameters)
    {
        $errors = [];
        if (empty($parameters["restApiKey"])) {
            $errors["restApiKey"] = "Missing Rest API key.";
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
     * @throws LocalizedException
     */
    public function execute($parameters = [])
    {
        $validation_result = $this->validate($parameters);
        if ($validation_result !== true) {
            return $this->_apiResponseInvalid->setErrors($validation_result);
        }
        $store = $this->_frameworkModelStore->load($parameters['store']);
        $endpoint = $this->buildEndpoint(
            static::ENDPOINT,
            $store,
            $this->_searchHelperConfig->getHostname($store)
        );
        $request = $this->getRequest();
        $request->setResponseModel($this->getResponse());
        $request->setEndpoint($endpoint);
        $request->setMethod(static::METHOD);
        $request->setData($parameters);

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
            . ($hostname) ?: $this->_searchHelperConfig->getHostname($store)
            . $endpoint;
    }
}
