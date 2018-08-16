<?php
namespace Klevu\Categorynavigation\Block\Categorynavigation;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Session\Generic;
class Tracking extends \Magento\Framework\View\Element\Template
{
	const KLEVU_PRESERVE_LAYOUT    = 2;
    const MAGENTO_DEFAULT     = 1;
    const KLEVU_TEMPLATE_LAYOUT = 3;
	
    /**
     * JSON of required tracking parameter for Klevu Product Click Tracking, based on current product
     * @return string
     * @throws Exception
     */
    public function getJsonTrackingData()
    {
        // Get the product
        $product = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Framework\Registry')->registry('current_product');
        $id = $product->getId();
        $name = $product->getName();
        $product_url = $product->getProductUrl();
        
        $api_key = \Magento\Framework\App\ObjectManager::getInstance()->get('\Klevu\Search\Helper\Config')->getJsApiKey();
            $product = [
                'klevu_apiKey' => $api_key,
                'klevu_term'   => '',
                'klevu_type'   => 'clicked',
                'klevu_productId' => $id,
                'klevu_productName' => $name,
                'klevu_productUrl' => $product_url,
                'Klevu_typeOfRecord' => 'KLEVU_PRODUCT'
            ];
            return json_encode($product);
    }
    
    public function isExtensionConfigured()
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get('Klevu\Search\Helper\Config')->isExtensionConfigured();
    }
    
    public function getCategoryNavigationTrackingUrl($store = null)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get('Klevu\Categorynavigation\Helper\Config')->getCategoryNavigationTrackingUrl($store);
    }

    public function getRefererRoute($store = null)
    {
       	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$requestInterface = $objectManager->get('Magento\Framework\App\RequestInterface');
		return $requestInterface->getRouteName();
    }

    public function getCategoryNavigationStatus()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$categorynavigationHelper = $objectManager->get('Klevu\Categorynavigation\Helper\Data');
		if($categorynavigationHelper->categoryLandingStatus() == static::KLEVU_PRESERVE_LAYOUT){
			return true;
		}
	}
	
	public function getCategoryName()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$redirect = $objectManager->get('Magento\Framework\App\Response\RedirectInterface');
		$refUrl = explode("/",$redirect->getRefererUrl());
		echo $refUrl;
		exit;
		$cat_url = explode(".",end($refUrl));
		if(isset($cat_url)) {
			$categorys = $objectManager->get('Magento\Catalog\Model\CategoryFactory')->create()
            ->getCollection()
            ->addAttributeToFilter('url_key',$cat_url[0])
            ->addAttributeToSelect(['name']);
			return $categorys->getFirstItem()->getName();
		}
	
	}
	
	
    
}