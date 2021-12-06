<?php

namespace Klevu\Categorynavigation\Test\Integration\Block\Product;

use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Store\App\Response\Redirect;
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
     * @magentoDataFixture loadProductFixtures
     * @magentoDataFixture loadCategoryProductAssociationFixtures
     */
    public function testOutputThemeV1_Product_Enabled()
    {
        $this->setupPhp5();

        $redirectInterfaceMock = $this->getRedirectInterfaceMock($this->prepareUrl('klevu-test-category-1'));
        $this->objectManager->addSharedInstance($redirectInterfaceMock, RedirectInterface::class);
        $this->objectManager->addSharedInstance($redirectInterfaceMock, Redirect::class);

        $this->dispatch($this->prepareUrl('klevu-simple-product-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
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
     * @magentoDataFixture loadProductFixtures
     * @magentoDataFixture loadCategoryProductAssociationFixtures
     */
    public function testOutputThemeV1_Product_PreserveLayout()
    {
        $this->setupPhp5();

        $redirectInterfaceMock = $this->getRedirectInterfaceMock($this->prepareUrl('klevu-test-category-1'));
        $this->objectManager->addSharedInstance($redirectInterfaceMock, RedirectInterface::class);
        $this->objectManager->addSharedInstance($redirectInterfaceMock, Redirect::class);

        $this->dispatch($this->prepareUrl('klevu-simple-product-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertStringContainsString(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertContains(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertContains(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
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
     * @magentoDataFixture loadProductFixtures
     * @magentoDataFixture loadCategoryProductAssociationFixtures
     */
    public function testOutputThemeV1_Product_Disabled()
    {
        $this->setupPhp5();

        $redirectInterfaceMock = $this->getRedirectInterfaceMock($this->prepareUrl('klevu-test-category-1'));
        $this->objectManager->addSharedInstance($redirectInterfaceMock, RedirectInterface::class);
        $this->objectManager->addSharedInstance($redirectInterfaceMock, Redirect::class);

        $this->dispatch($this->prepareUrl('klevu-simple-product-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
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
     * @magentoDataFixture loadProductFixtures
     * @magentoDataFixture loadCategoryProductAssociationFixtures
     */
    public function testOutputThemeV1_Product_NoReferer()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-simple-product-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
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
     * @magentoDataFixture loadProductFixtures
     * @magentoDataFixture loadCategoryProductAssociationFixtures
     */
    public function testOutputThemeV1_Product_SearchDisabled()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-simple-product-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
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
     * @magentoDataFixture loadProductFixtures
     * @magentoDataFixture loadCategoryProductAssociationFixtures
     */
    public function testOutputThemeV1_HomePage_PreserveLayout()
    {
        $this->setupPhp5();

        $redirectInterfaceMock = $this->getRedirectInterfaceMock($this->prepareUrl('klevu-test-category-1'));
        $this->objectManager->addSharedInstance($redirectInterfaceMock, RedirectInterface::class);
        $this->objectManager->addSharedInstance($redirectInterfaceMock, Redirect::class);

        $this->dispatch('/');

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
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
     * @magentoDataFixture loadProductFixtures
     * @magentoDataFixture loadCategoryProductAssociationFixtures
     */
    public function testOutputThemeV2_Product_Enabled()
    {
        $this->setupPhp5();

        $redirectInterfaceMock = $this->getRedirectInterfaceMock($this->prepareUrl('klevu-test-category-1'));
        $this->objectManager->addSharedInstance($redirectInterfaceMock, RedirectInterface::class);
        $this->objectManager->addSharedInstance($redirectInterfaceMock, Redirect::class);

        $this->dispatch($this->prepareUrl('klevu-simple-product-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
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
     * @magentoDataFixture loadProductFixtures
     * @magentoDataFixture loadCategoryProductAssociationFixtures
     */
    public function testOutputThemeV2_Product_PreserveLayout()
    {
        $this->setupPhp5();

        $redirectInterfaceMock = $this->getRedirectInterfaceMock($this->prepareUrl('klevu-test-category-1'));
        $this->objectManager->addSharedInstance($redirectInterfaceMock, RedirectInterface::class);
        $this->objectManager->addSharedInstance($redirectInterfaceMock, Redirect::class);

        $this->dispatch($this->prepareUrl('klevu-simple-product-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertStringContainsString(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertContains(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertContains(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
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
     * @magentoDataFixture loadProductFixtures
     * @magentoDataFixture loadCategoryProductAssociationFixtures
     */
    public function testOutputThemeV2_Product_Disabled()
    {
        $this->setupPhp5();

        $redirectInterfaceMock = $this->getRedirectInterfaceMock($this->prepareUrl('klevu-test-category-1'));
        $this->objectManager->addSharedInstance($redirectInterfaceMock, RedirectInterface::class);
        $this->objectManager->addSharedInstance($redirectInterfaceMock, Redirect::class);

        $this->dispatch($this->prepareUrl('klevu-simple-product-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
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
     * @magentoDataFixture loadProductFixtures
     * @magentoDataFixture loadCategoryProductAssociationFixtures
     */
    public function testOutputThemeV2_Product_NoReferer()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-simple-product-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
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
     * @magentoDataFixture loadProductFixtures
     * @magentoDataFixture loadCategoryProductAssociationFixtures
     */
    public function testOutputThemeV2_Product_SearchDisabled()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-simple-product-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
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
     * @magentoDataFixture loadProductFixtures
     * @magentoDataFixture loadCategoryProductAssociationFixtures
     */
    public function testOutputThemeV2_HomePage_PreserveLayout()
    {
        $this->setupPhp5();

        $redirectInterfaceMock = $this->getRedirectInterfaceMock($this->prepareUrl('klevu-test-category-1'));
        $this->objectManager->addSharedInstance($redirectInterfaceMock, RedirectInterface::class);
        $this->objectManager->addSharedInstance($redirectInterfaceMock, Redirect::class);

        $this->dispatch('/');

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertStringNotContainsString(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
                $responseBody,
                'Response contains category product click tracking URL'
            );
        } else {
            $this->assertNotContains(
                'function categoryAnaylticsProductClickKlevu()',
                $responseBody,
                'categoryAnaylticsProductClickKlevu JavaScript function definition'
            );
            $this->assertNotContains(
                "//stats.klevu.com/analytics/categoryProductClickTracking'",
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
     * @param $refererUrlReturn
     * @return RedirectInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getRedirectInterfaceMock($refererUrlReturn)
    {
        if (method_exists($this, 'createConfiguredMock')) {
            $redirectInterfaceMock = $this->createConfiguredMock(RedirectInterface::class, [
                'getRefererUrl' => $refererUrlReturn
            ]);
        } else {
            $redirectInterfaceMock = $this->getMockBuilder(RedirectInterface::class)
                ->disableOriginalConstructor()
                ->disableOriginalClone()
                ->disableArgumentCloning()
                ->getMock();
            $redirectInterfaceMock->expects($this->any())
                ->method('getRefererUrl')
                ->willReturn($refererUrlReturn);
        }

        return $redirectInterfaceMock;
    }

    /**
     * Loads category creation scripts because annotations use a relative path
     *  from integration tests root
     */
    public static function loadCategoryFixtures()
    {
        include __DIR__ . '/../../_files/categoryFixtures.php';
    }

    /**
     * Rolls back category creation scripts because annotations use a relative path
     *  from integration tests root
     */
    public static function loadCategoryFixturesRollback()
    {
        include __DIR__ . '/../../_files/categoryFixtures_rollback.php';
    }

    /**
     * Loads product creation scripts because annotations use a relative path
     *  from integration tests root
     */
    public static function loadProductFixtures()
    {
        include __DIR__ . '/../../_files/productFixtures.php';
    }

    /**
     * Rolls back product creation scripts because annotations use a relative path
     *  from integration tests root
     */
    public static function loadProductFixturesRollback()
    {
        include __DIR__ . '/../../_files/productFixtures_rollback.php';
    }

    /**
     * Loads category product association scripts because annotations use a relative path
     *  from integration tests root
     */
    public static function loadCategoryProductAssociationFixtures()
    {
        include __DIR__ . '/../../_files/categoryProductAssociationFixtures.php';
    }
}
