<?php
/** @noinspection PhpUnhandledExceptionInspection */

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();

/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
$defaultStoreView = $storeManager->getDefaultStoreView();

/** @var EavConfig $eavConfig */
$eavConfig = $objectManager->get(EavConfig::class);

$configurableAttribute = $eavConfig->getAttribute('catalog_product', 'klevu_test_configurable');
$configurableAttributeOptions = $configurableAttribute->getOptions();

$fixtures = [
    // Standalone Simple
    [
        'type_id' => 'simple',
        'sku' => 'klevu_simple_1',
        'name' => '[Klevu] Simple Product 1',
        'description' => '[Klevu Test Fixtures] Simple product 1 (Enabled; Visibility Both)',
        'short_description' => '[Klevu Test Fixtures] Simple product 1',
        'attribute_set_id' => 4,
        'website_ids' => [
            $defaultStoreView->getWebsiteId(),
        ],
        'price' => 10.00,
        'special_price' => 4.99,
        'weight' => 1,
        'tax_class_id' => 2,
        'meta_title' => '[Klevu] Simple Product 1',
        'meta_description' => '[Klevu Test Fixtures] Simple product 1',
        'visibility' => Visibility::VISIBILITY_BOTH,
        'status' => Status::STATUS_ENABLED,
        'stock_data' => [
            'use_config_manage_stock'   => 1,
            'qty'                       => 100,
            'is_qty_decimal'            => 0,
            'is_in_stock'               => 1,
        ],
        'url_key' => 'klevu-simple-product-1',
    ],
];
