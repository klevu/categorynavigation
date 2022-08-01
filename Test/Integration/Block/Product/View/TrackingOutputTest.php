<?php

namespace Klevu\Categorynavigation\Test\Integration\Block\Product\View;

use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController as AbstractControllerTestCase;

class TrackingOutputTest extends AbstractControllerTestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string
     */
    private $urlSuffix;

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/general/enabled 1
     * @magentoConfigFixture default_store klevu_search/general/enabled 1
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default/klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default_store klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default/klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default_store klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default/klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v1
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v1
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testOutputThemeV1_Category_Enabled()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductViewTracking",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductViewTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/general/enabled 1
     * @magentoConfigFixture default_store klevu_search/general/enabled 1
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default/klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default_store klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default/klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default_store klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default/klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v1
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v1
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testOutputThemeV1_Category_PreserveLayout()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString(
                'klevu_search_product_tracking = {"klevu_apiKey":"klevu-1234567890"',
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertStringContainsString(
                "//stats.klevu.com/analytics/categoryProductViewTracking",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertContains(
                'klevu_search_product_tracking = {"klevu_apiKey":"klevu-1234567890"',
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertContains(
                "//stats.klevu.com/analytics/categoryProductViewTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/general/enabled 1
     * @magentoConfigFixture default_store klevu_search/general/enabled 1
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default/klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default_store klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default/klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default_store klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default/klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v1
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v1
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testOutputThemeV1_Category_Disabled()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductViewTracking",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductViewTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/general/enabled 0
     * @magentoConfigFixture default_store klevu_search/general/enabled0
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default/klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default_store klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default/klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default_store klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default/klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v1
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v1
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testOutputThemeV1_Category_SearchDisabled()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductViewTracking",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductViewTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/general/enabled 1
     * @magentoConfigFixture default_store klevu_search/general/enabled 1
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default/klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default_store klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default/klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default_store klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default/klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v1
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v1
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testOutputThemeV1_HomePage_PreserveLayout()
    {
        $this->setupPhp5();

        $this->dispatch('/');

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductViewTracking",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductViewTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/general/enabled 1
     * @magentoConfigFixture default_store klevu_search/general/enabled 1
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default/klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default_store klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default/klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default_store klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default/klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v2
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v2
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testOutputThemeV2_Category_Enabled()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductViewTracking",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductViewTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/general/enabled 1
     * @magentoConfigFixture default_store klevu_search/general/enabled 1
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default/klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default_store klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default/klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default_store klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default/klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v2
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v2
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testOutputThemeV2_Category_PreserveLayout()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString(
                'klevu_search_product_tracking = {"klevu_apiKey":"klevu-1234567890"',
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertStringContainsString(
                "//stats.klevu.com/analytics/categoryProductViewTracking",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertContains(
                'klevu_search_product_tracking = {"klevu_apiKey":"klevu-1234567890"',
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertContains(
                "//stats.klevu.com/analytics/categoryProductViewTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/general/enabled 1
     * @magentoConfigFixture default_store klevu_search/general/enabled 1
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default/klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default_store klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default/klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default_store klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default/klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v2
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v2
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testOutputThemeV2_Category_Disabled()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductViewTracking",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductViewTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/general/enabled 0
     * @magentoConfigFixture default_store klevu_search/general/enabled 0
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default/klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default_store klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default/klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default_store klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default/klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v2
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v2
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testOutputThemeV2_Category_SearchDisabled()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductViewTracking",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductViewTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/general/enabled 1
     * @magentoConfigFixture default_store klevu_search/general/enabled 1
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default/klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default_store klevu_search/general/js_api_key klevu-1234567890
     * @magentoConfigFixture default/klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default_store klevu_search/general/rest_api_key ABCDE12345
     * @magentoConfigFixture default/klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/category_navigation_tracking_url stats.klevu.com
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v2
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v2
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testOutputThemeV2_HomePage_PreserveLayout()
    {
        $this->setupPhp5();

        $this->dispatch('/');

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductViewTracking",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                "klevu_search_product_tracking = ",
                $responseBody,
                'Product Tracking JS variable defined'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductViewTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        }
    }

    /**
     * @return void
     * @todo Move to setUp when PHP 5.x is no longer supported
     */
    private function setupPhp5()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->scopeConfig = $this->objectManager->get(ScopeConfigInterface::class);
        $this->urlSuffix = $this->scopeConfig->getValue(
            CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Prepare url to dispatch
     *
     * @param string $urlKey
     * @param bool $addSuffix
     * @return string
     */
    private function prepareUrl($urlKey, $addSuffix = true)
    {
        return $addSuffix ? '/' . $urlKey . $this->urlSuffix : '/' . $urlKey;
    }

    /**
     * Loads category creation scripts because annotations use a relative path
     *  from integration tests root
     */
    public static function loadCategoryFixtures()
    {
        include __DIR__ . '/../../../_files/categoryFixtures.php';
    }

    /**
     * Rolls back category creation scripts because annotations use a relative path
     *  from integration tests root
     */
    public static function loadCategoryFixturesRollback()
    {
        include __DIR__ . '/../../../_files/categoryFixtures_rollback.php';
    }
}
