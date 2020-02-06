<?php

/**
 * Class \Klevu\Search\Model\Observer
 *
 * @method UpdateCategoryPageLayout($flag)
 * 
 */
namespace Klevu\Categorynavigation\Model\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\LayoutInterface;
use \Klevu\Categorynavigation\Helper\Data;

class CateoryNavigationUrl implements ObserverInterface
{


    public function __construct(
        \Klevu\Categorynavigation\Helper\Data $categorynavigationHelper,
		\Klevu\Search\Helper\Config $searchHelperConfig,
		\Klevu\Categorynavigation\Helper\Config $categorynavigationHelperConfig,
		\Magento\Framework\App\Request\Http $request,
		\Klevu\Categorynavigation\Model\Api\Action\CategoryNavigationUrl $apiActionCategoryNavigationURL,
		\Magento\Framework\App\Config\ConfigResource\ConfigInterface $configInterface
    ) {
        $this->_categorynavigationHelper = $categorynavigationHelper;
		$this->request = $request;
		$this->_apiActionCategoryNavigationURL = $apiActionCategoryNavigationURL;
		$this->_searchHelperConfig = $searchHelperConfig;
		$this->_categorynavigationHelperConfig = $categorynavigationHelperConfig;
		$this->_configInterface = $configInterface;
    }

    /**
     * If Cat Nav feature enabled then Cat Nav URL value will be saved.
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {	
	   
		$store = $this->request->getParam("store");
		if ($store !== null) {
			$config_state = $this->request->getParam('groups');
			if(isset($config_state['general']['fields']['category_navigation_url']['value'])) {
				$value_categorylanding = $config_state['general']['fields']['category_navigation_url']['value'];
				$new_value = $value_categorylanding;
				if($this->_categorynavigationHelper->getCategoryNavigationUrl() !== $new_value){
				 	
					$restApi = $this->_searchHelperConfig->getRestApiKey($this->request->getParam("store"));
					$param =  ["restApiKey" => $restApi,"store" => $this->request->getParam("store")];
					$response = $this->_apiActionCategoryNavigationURL->execute($param);
					if ($response->isSuccess()) {
						$category_navigation_url = $response->getcategoryNavigationUrl();
						if(!empty($category_navigation_url)) {
							$this->_categorynavigationHelperConfig->setCategoryNavigationUrl($category_navigation_url,$store);
						}
					}
					
				}
			}
		}

    }
}
