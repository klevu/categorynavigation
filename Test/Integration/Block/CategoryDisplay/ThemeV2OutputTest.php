<?php

namespace Klevu\Categorynavigation\Test\Integration\Block\CategoryDisplay;

use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController as AbstractControllerTestCase;

class ThemeV2OutputTest extends AbstractControllerTestCase
{
    const KLEVU_LANDING_ELEMENT_REGEX = '#<div +([a-zA-Z-_="\']+ +)*class=(\'|") *((-?[_a-zA-Z]+[_a-zA-Z0-9-]*) +)*klevuLanding( +(-?[_a-zA-Z]+[_a-zA-Z0-9-]*))* *(\'|")( +[a-zA-Z-_="\']+)* *></div>#';

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
     * @magentoDataFixture loadCategoryFixtures
     */
    public function testOutputThemeV2_Enabled()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        $this->assertRegExp(static::KLEVU_LANDING_ELEMENT_REGEX, $responseBody);
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
    public function testOutputThemeV2_PreserveLayout()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        $this->assertNotRegExp(static::KLEVU_LANDING_ELEMENT_REGEX, $responseBody);
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
    public function testOutputThemeV2_Disabled()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        $this->assertNotRegExp(static::KLEVU_LANDING_ELEMENT_REGEX, $responseBody);
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
    public function testOutputThemeV1()
    {
        $this->setupPhp5();

        $this->dispatch($this->prepareUrl('klevu-test-category-1'));

        $response = $this->getResponse();
        $responseBody = $response->getBody();
        $this->assertSame(200, $response->getHttpResponseCode());

        $this->assertNotRegExp(static::KLEVU_LANDING_ELEMENT_REGEX, $responseBody);
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
