<?php
/** @noinspection PhpUnhandledExceptionInspection */

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var CategoryCollection $categoryCollection */
$categoryCollection = $objectManager->get(CategoryCollectionFactory::class)->create();
$categoryCollection->addAttributeToFilter('url_key', ['in' => [
    'klevu-test-category-1',
    'klevu-test-category-1-1',
]]);
$categoryCollection->load();

/** @var ProductCollection $productCollection */
$productCollection = $objectManager->get(ProductCollectionFactory::class)->create();
$productCollection->addAttributeToFilter('sku', ['in' => [
    'klevu_simple_1',
]]);

/** @var CategoryLinkManagementInterface $categoryLinkManagement */
$categoryLinkManagement = $objectManager->get(CategoryLinkManagementInterface::class);
foreach ($productCollection as $product) {
    $categoryLinkManagement->assignProductToCategories(
        $product->getSku(),
        $categoryCollection->getColumnValues('entity_id')
    );
}
