<?php

namespace Klevu\Categorynavigation\Test\Integration\Block\ThemeV2;

// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController as AbstractControllerTestCase;

class PageOutputTest extends AbstractControllerTestCase
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
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v2
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v2
     * @magentoConfigFixture default/klevu_search/developer/lazyload_js_catnav 0
     * @magentoConfigFixture default_store klevu_search/developer/lazyload_js_catnav 0
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testThemeV2JavaScriptOutputToCategory_Enabled()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js.klevu.com&#x2F;core&#x2F;v2&#x2F;klevu.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'Library JS include is present in response body'
            );
            $this->assertStringContainsString(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js.klevu.com&#x2F;theme&#x2F;default&#x2F;v2&#x2F;catnav-theme.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'CatNav JS include is present in response body'
            );
        } else {
            $this->assertContains(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js.klevu.com&#x2F;core&#x2F;v2&#x2F;klevu.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'Library JS include is present in response body'
            );
            $this->assertContains(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js.klevu.com&#x2F;theme&#x2F;default&#x2F;v2&#x2F;catnav-theme.js"></script>',  // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'CatNav JS include is present in response body'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v2
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v2
     * @magentoConfigFixture default/klevu_search/developer/lazyload_js_catnav 1
     * @magentoConfigFixture default_store klevu_search/developer/lazyload_js_catnav 1
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testThemeV2JavaScriptOutputToCategory_EnabledViaLazyLoad()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringNotContainsString(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js.klevu.com&#x2F;theme&#x2F;default&#x2F;v2&#x2F;catnav-theme.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'CatNav JS include is not present in response body'
            );
            $this->assertStringContainsString(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js.klevu.com&#x2F;theme&#x2F;default&#x2F;v2&#x2F;catnav-theme.lazyload.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'Lazyload CatNav JS include is present in response body'
            );
        } else {
            $this->assertNotContains(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js.klevu.com&#x2F;theme&#x2F;default&#x2F;v2&#x2F;catnav-theme.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'CatNav JS include is not present in response body'
            );
            $this->assertContains(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js.klevu.com&#x2F;theme&#x2F;default&#x2F;v2&#x2F;catnav-theme.lazyload.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'Lazyload CatNav JS include is present in response body'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v2
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v2
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js-test.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/lazyload_js_catnav 0
     * @magentoConfigFixture default_store klevu_search/developer/lazyload_js_catnav 0
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testThemeV2JavaScriptOutputToCategory_Enabled_SpecifiedJsHost()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js.klevu.com&#x2F;core&#x2F;v2&#x2F;klevu.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'Library JS include is present in response body'
            );
            $this->assertStringNotContainsString(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js.klevu.com&#x2F;theme&#x2F;default&#x2F;v2&#x2F;catnav-theme.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'CatNav JS include is present in response body'
            );
        } else {
            $this->assertNotContains(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js.klevu.com&#x2F;core&#x2F;v2&#x2F;klevu.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'Library JS include is present in response body'
            );
            $this->assertNotContains(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js.klevu.com&#x2F;theme&#x2F;default&#x2F;v2&#x2F;catnav-theme.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'CatNav JS include is present in response body'
            );
        }
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js-test.klevu.com&#x2F;core&#x2F;v2&#x2F;klevu.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'Library JS include is present in response body'
            );
            $this->assertStringContainsString(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js-test.klevu.com&#x2F;theme&#x2F;default&#x2F;v2&#x2F;catnav-theme.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'CatNav JS include is present in response body'
            );
        } else {
            $this->assertContains(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js-test.klevu.com&#x2F;core&#x2F;v2&#x2F;klevu.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'Library JS include is present in response body'
            );
            $this->assertContains(
                '<script type="text&#x2F;javascript" src="https&#x3A;&#x2F;&#x2F;js-test.klevu.com&#x2F;theme&#x2F;default&#x2F;v2&#x2F;catnav-theme.js"></script>', // phpcs:ignore Generic.Files.LineLength.TooLong
                $responseBody,
                'CatNav JS include is present in response body'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v2
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v2
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testThemeV2JavaScriptOutputToCategory_PreserveLayout()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                '<script type="text/javascript" src="https://js.klevu.com/theme/default/v2/catnav-theme.js"></script>',
                $responseBody,
                'CatNav JS include is present in response body'
            );
        } else {
            $this->assertNotContains(
                '<script type="text/javascript" src="https://js.klevu.com/theme/default/v2/catnav-theme.js"></script>',
                $responseBody,
                'CatNav JS include is present in response body'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v2
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v2
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testThemeV2JavaScriptOutputToCategory_Disabled()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                '<script type="text/javascript" src="https://js.klevu.com/theme/default/v2/catnav-theme.js"></script>',
                $responseBody,
                'CatNav JS include is present in response body'
            );
        } else {
            $this->assertNotContains(
                '<script type="text/javascript" src="https://js.klevu.com/theme/default/v2/catnav-theme.js"></script>',
                $responseBody,
                'CatNav JS include is present in response body'
            );
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     * @magentoCache all disabled
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default/klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default_store klevu_search/general/js_url js.klevu.com
     * @magentoConfigFixture default/klevu_search/developer/theme_version v1
     * @magentoConfigFixture default_store klevu_search/developer/theme_version v1
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testThemeV2JavaScriptOutputToCategory_ThemeV1()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                '<script type="text/javascript" src="https://js.klevu.com/theme/default/v2/catnav-theme.js"></script>',
                $responseBody,
                'CatNav JS include is present in response body'
            );
        } else {
            $this->assertNotContains(
                '<script type="text/javascript" src="https://js.klevu.com/theme/default/v2/catnav-theme.js"></script>',
                $responseBody,
                'CatNav JS include is present in response body'
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
     *
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
}
