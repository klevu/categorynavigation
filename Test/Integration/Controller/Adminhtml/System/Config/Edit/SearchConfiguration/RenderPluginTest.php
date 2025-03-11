<?php
// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Klevu\Categorynavigation\Test\Integration\Controller\Adminhtml\System\Config\Edit\SearchConfiguration;

use Klevu\Search\Api\Service\Account\GetFeaturesInterface;
use Klevu\Search\Service\Account\GetFeatures;
use Klevu\Search\Service\Account\Model\AccountFeatures;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\Config\Storage\Writer as ScopeConfigWriter;
use Magento\Framework\App\Config\Storage\WriterInterface as ScopeConfigWriterInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\TestCase\AbstractBackendController as AbstractBackendControllerTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class RenderPluginTest extends AbstractBackendControllerTestCase
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var MockObject&LoggerInterface
     */
    private $loggerMock;

    /**
     * @var MockObject&AccountFeaturesInterface
     */
    private $accountFeaturesMock;

    /**
     * @var MockObject&GetFeaturesInterface
     */
    private $getFeaturesMock;

    /**
     * @var MockObject&ScopeConfigWriterInterface
     */
    private $scopeConfigWriterMock;

    /**
     * @var string
     */
    protected $resource = 'Klevu_Search::config_search';

    /**
     * @var int
     */
    protected $expectedNoAccessResponseCode = 302;

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/admin/url/use_custom 1
     * @magentoConfigFixture default_store admin/url/use_custom 1
     * @magentoConfigFixture default/admin/url/custom http://localhost/
     * @magentoConfigFixture default_store admin/url/custom http://localhost/
     * @magentoConfigFixture default/klevu_search/add_to_cart/enabledaddtocartfront 0
     * @magentoConfigFixture default_store klevu_search/add_to_cart/enabledaddtocartfront 0
     */
    public function testRender_DefaultScope()
    {
        $this->setupPhp5();

        $this->scopeConfigWriterMock->expects($this->never())
            ->method('save');

        $request = $this->getRequest();
        $request->setParam('section', 'klevu_search');
        $request->setMethod('GET');

        $this->dispatch($this->getAdminFrontName() . '/admin/system_config/edit');

        $response = $this->getResponse();
        $httpResponseCode = $response->getHttpResponseCode();
        $this->assertNotSame(404, $httpResponseCode);
        $this->assertNotSame($this->expectedNoAccessResponseCode, $httpResponseCode);

        $responseBody = $response->getBody();
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('<div id="system_config_tabs"', $responseBody);
        } else {
            $this->assertContains('<div id="system_config_tabs"', $responseBody);
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<fieldset[^>]+id="klevu_search_categorylanding"#',
                $responseBody
            );
        } else {
            $this->assertNotRegExp('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        }

        $matches = [];
        preg_match(
            '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(0, $matches);
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation".*?</tr>#s',
                $responseBody
            );
            $this->assertDoesNotMatchRegularExpression(
                '#<(input|select).*?id="klevu_search_categorylanding_enabledcategorynavigation"#s',
                $responseBody
            );
        } else {
            $this->assertNotRegExp(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation".*?</tr>#s',
                $responseBody
            );
            $this->assertNotRegexp(
                '#<(input|select).*?id="klevu_search_categorylanding_enabledcategorynavigation"#s',
                $responseBody
            );
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/admin/url/use_custom 1
     * @magentoConfigFixture default_store admin/url/use_custom 1
     * @magentoConfigFixture default/admin/url/custom http://localhost/
     * @magentoConfigFixture default_store admin/url/custom http://localhost/
     * @magentoConfigFixture default/klevu_search/add_to_cart/enabledaddtocartfront 0
     * @magentoConfigFixture default_store klevu_search/add_to_cart/enabledaddtocartfront 0
     */
    public function testRender_WebsiteScope()
    {
        $this->setupPhp5();
        $defaultStore = $this->storeManager->getDefaultStoreView();

        $this->scopeConfigWriterMock->expects($this->never())
            ->method('save');

        $request = $this->getRequest();
        $request->setParam('website', $defaultStore->getWebsiteId());
        $request->setParam('section', 'klevu_search');
        $request->setMethod('GET');

        $this->dispatch($this->getAdminFrontName() . '/admin/system_config/edit');

        $response = $this->getResponse();
        $httpResponseCode = $response->getHttpResponseCode();
        $this->assertNotSame(404, $httpResponseCode);
        $this->assertNotSame($this->expectedNoAccessResponseCode, $httpResponseCode);

        $responseBody = $response->getBody();
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('<div id="system_config_tabs"', $responseBody);
        } else {
            $this->assertContains('<div id="system_config_tabs"', $responseBody);
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<fieldset[^>]+id="klevu_search_categorylanding"#',
                $responseBody
            );
        } else {
            $this->assertNotRegExp('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        }

        $matches = [];
        preg_match(
            '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(0, $matches);
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation".*?</tr>#s',
                $responseBody
            );
            $this->assertDoesNotMatchRegularExpression(
                '#<(input|select).*?id="klevu_search_categorylanding_enabledcategorynavigation"#s',
                $responseBody
            );
        } else {
            $this->assertNotRegExp(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation".*?</tr>#s',
                $responseBody
            );
            $this->assertNotRegexp(
                '#<(input|select).*?id="klevu_search_categorylanding_enabledcategorynavigation"#s',
                $responseBody
            );
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/admin/url/use_custom 1
     * @magentoConfigFixture default_store admin/url/use_custom 1
     * @magentoConfigFixture default/admin/url/custom http://localhost/
     * @magentoConfigFixture default_store admin/url/custom http://localhost/
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default/klevu_search/add_to_cart/enabledaddtocartfront 0
     * @magentoConfigFixture default_store klevu_search/add_to_cart/enabledaddtocartfront 0
     */
    public function testRender_StoreScope_CategoryNavigationAvailable_PreserveLayoutAvailable_DefaultConfig()
    {
        $this->setupPhp5();
        $defaultStore = $this->storeManager->getDefaultStoreView();

        $this->scopeConfigWriterMock->expects($this->never())
            ->method('save');

        $this->accountFeaturesMock->method('isFeatureAvailable')->willReturnCallback(
            static function ($feature, $strict = false) {
                switch ($feature) {
                    case AccountFeatures::PM_FEATUREFLAG_CATEGORY_NAVIGATION:
                    case AccountFeatures::PM_FEATUREFLAG_PRESERVES_LAYOUT:
                        $return = true;
                        break;

                    default:
                        $return = false;
                        break;
                }

                return $return;
            }
        );

        $request = $this->getRequest();
        $request->setParam('store', $defaultStore->getId());
        $request->setParam('section', 'klevu_search');
        $request->setMethod('GET');

        $this->dispatch($this->getAdminFrontName() . '/admin/system_config/edit');

        $response = $this->getResponse();
        $httpResponseCode = $response->getHttpResponseCode();
        $this->assertNotSame(404, $httpResponseCode);
        $this->assertNotSame($this->expectedNoAccessResponseCode, $httpResponseCode);

        $responseBody = $response->getBody();

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        } else {
            $this->assertRegExp('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        } else {
            $this->assertNotRegExp(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        }

        $matches = [];
        preg_match(
            '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation".*?</tr>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering row');
        $catNavOrderingRow = current($matches);
        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString('Switch to Store View scope to manage', $catNavOrderingRow);
            $this->assertStringNotContainsString(
                '<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>',
                $catNavOrderingRow
            );
        } else {
            $this->assertNotContains('Switch to Store View scope to manage', $catNavOrderingRow);
            $this->assertNotContains('<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>', $catNavOrderingRow);
        }

        // Ordering and Rendering
        $matches = [];
        preg_match(
            '#<select[^>]+id="klevu_search_categorylanding_enabledcategorynavigation".*?>.*?</select>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering field');
        $catNavOrderingField = current($matches);
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertRegExp('#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s', $catNavOrderingField);
            $this->assertRegExp(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertRegExp(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertNotRegExp('#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s', $catNavOrderingField);
        }

        $matches = [];
        preg_match('#<p[^>]+class="note"[^>]*>.*?</p>#s', $catNavOrderingRow, $matches);
        $this->assertCount(1, $matches, 'CatNav Ordering comment');
        $catNavOrderingComment = current($matches);

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertStringContainsString('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
            $this->assertStringContainsString('<strong>Preserve your Magento layout:</strong>', $catNavOrderingComment);
        } else {
            $this->assertContains('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertContains('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
            $this->assertContains('<strong>Preserve your Magento layout:</strong>', $catNavOrderingComment);
        }

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            // Maximum Number of Products Per Category
            $this->assertMatchesRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertMatchesRegularExpression(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertMatchesRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s',
                $responseBody
            );
        } else {
            // Maximum Number of Products Per Category
            $this->assertRegExp(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertRegExp(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertRegExp('#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s', $responseBody);
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/admin/url/use_custom 1
     * @magentoConfigFixture default_store admin/url/use_custom 1
     * @magentoConfigFixture default/admin/url/custom http://localhost/
     * @magentoConfigFixture default_store admin/url/custom http://localhost/
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default/klevu_search/add_to_cart/enabledaddtocartfront 0
     * @magentoConfigFixture default_store klevu_search/add_to_cart/enabledaddtocartfront 0
     */
    public function testRender_StoreScope_CategoryNavigationAvailable_PreserveLayoutUnavailable_DefaultConfig()
    {
        $this->setupPhp5();
        $defaultStore = $this->storeManager->getDefaultStoreView();

        $this->scopeConfigWriterMock->expects($this->never())
            ->method('save');

        $this->accountFeaturesMock->method('isFeatureAvailable')->willReturnCallback(
            static function ($feature, $strict = false) {
                switch ($feature) {
                    case AccountFeatures::PM_FEATUREFLAG_CATEGORY_NAVIGATION:
                        $return = true;
                        break;

                    case AccountFeatures::PM_FEATUREFLAG_PRESERVES_LAYOUT:
                    default:
                        $return = false;
                        break;
                }

                return $return;
            }
        );

        $request = $this->getRequest();
        $request->setParam('store', $defaultStore->getId());
        $request->setParam('section', 'klevu_search');
        $request->setMethod('GET');

        $this->dispatch($this->getAdminFrontName() . '/admin/system_config/edit');

        $response = $this->getResponse();
        $httpResponseCode = $response->getHttpResponseCode();
        $this->assertNotSame(404, $httpResponseCode);
        $this->assertNotSame($this->expectedNoAccessResponseCode, $httpResponseCode);

        $responseBody = $response->getBody();

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        } else {
            $this->assertRegExp('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        } else {
            $this->assertNotRegExp(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        }

        // Ordering and Rendering
        $matches = [];
        preg_match(
            '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation".*?</tr>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering row');
        $catNavOrderingRow = current($matches);
        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString('Switch to Store View scope to manage', $catNavOrderingRow);
            $this->assertStringNotContainsString(
                '<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>',
                $catNavOrderingRow
            );
        } else {
            $this->assertNotContains('Switch to Store View scope to manage', $catNavOrderingRow);
            $this->assertNotContains('<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>', $catNavOrderingRow);
        }

        $matches = [];
        preg_match(
            '#<select[^>]+id="klevu_search_categorylanding_enabledcategorynavigation".*?>.*?</select>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering field');
        $catNavOrderingField = current($matches);
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertRegExp('#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s', $catNavOrderingField);
            $this->assertRegExp('#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s', $catNavOrderingField);
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertDoesNotMatchRegularExpression(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertNotRegExp(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertNotRegExp(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        }

        $matches = [];
        preg_match('#<p[^>]+class="note"[^>]*>.*?</p>#s', $catNavOrderingRow, $matches);
        $this->assertCount(1, $matches, 'CatNav Ordering comment');
        $catNavOrderingComment = current($matches);

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertStringContainsString('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
        } else {
            $this->assertContains('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertContains('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
        }

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                '<strong>Preserve your Magento layout:</strong>',
                $catNavOrderingComment
            );
        } else {
            $this->assertNotContains('<strong>Preserve your Magento layout:</strong>', $catNavOrderingComment);
        }

        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            // Maximum Number of Products Per Category
            $this->assertDoesNotMatchRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertDoesNotMatchRegularExpression(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertDoesNotMatchRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s',
                $responseBody
            );
        } else {
            // Maximum Number of Products Per Category
            $this->assertNotRegExp(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertNotRegExp(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertNotRegExp(
                '#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s',
                $responseBody
            );
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/admin/url/use_custom 1
     * @magentoConfigFixture default_store admin/url/use_custom 1
     * @magentoConfigFixture default/admin/url/custom http://localhost/
     * @magentoConfigFixture default_store admin/url/custom http://localhost/
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default/klevu_search/add_to_cart/enabledaddtocartfront 0
     * @magentoConfigFixture default_store klevu_search/add_to_cart/enabledaddtocartfront 0
     */
    public function testRender_StoreScope_CategoryNavigationUnavailable_PreserveLayoutAvailable_DefaultConfig()
    {
        $this->setupPhp5();
        $defaultStore = $this->storeManager->getDefaultStoreView();

        $this->scopeConfigWriterMock->expects($this->never())
            ->method('save');

        $this->accountFeaturesMock->method('isFeatureAvailable')->willReturnCallback(
            static function ($feature, $strict = false) {
                switch ($feature) {
                    case AccountFeatures::PM_FEATUREFLAG_PRESERVES_LAYOUT:
                        $return = true;
                        break;

                    case AccountFeatures::PM_FEATUREFLAG_CATEGORY_NAVIGATION:
                    default:
                        $return = false;
                        break;
                }

                return $return;
            }
        );

        $request = $this->getRequest();
        $request->setParam('store', $defaultStore->getId());
        $request->setParam('section', 'klevu_search');
        $request->setMethod('GET');

        $this->dispatch($this->getAdminFrontName() . '/admin/system_config/edit');

        $response = $this->getResponse();
        $httpResponseCode = $response->getHttpResponseCode();
        $this->assertNotSame(404, $httpResponseCode);
        $this->assertNotSame($this->expectedNoAccessResponseCode, $httpResponseCode);

        $responseBody = $response->getBody();

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        } else {
            $this->assertRegExp('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        } else {
            $this->assertNotRegExp(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        }

        // Ordering and Rendering
        $matches = [];
        preg_match(
            '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation".*?</tr>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering row');
        $catNavOrderingRow = current($matches);
        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString('Switch to Store View scope to manage', $catNavOrderingRow);
        } else {
            $this->assertNotContains('Switch to Store View scope to manage', $catNavOrderingRow);
        }
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString(
                '<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>',
                $catNavOrderingRow
            );
        } else {
            $this->assertContains('<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>', $catNavOrderingRow);
        }

        $matches = [];
        preg_match(
            '#<select[^>]+id="klevu_search_categorylanding_enabledcategorynavigation".*?>.*?</select>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering field');
        $catNavOrderingField = current($matches);
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('disabled', $catNavOrderingField);
        } else {
            $this->assertContains('disabled', $catNavOrderingField);
        }
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertRegExp(
                '#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertRegExp(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertRegExp(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertNotRegExp('#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s', $catNavOrderingField);
        }

        $matches = [];
        preg_match('#<p[^>]+class="note"[^>]*>.*?</p>#s', $catNavOrderingRow, $matches);
        $this->assertCount(1, $matches, 'CatNav Ordering comment');
        $catNavOrderingComment = current($matches);

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertStringContainsString('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
            $this->assertStringContainsString('<strong>Preserve your Magento layout:</strong>', $catNavOrderingComment);
        } else {
            $this->assertContains('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertContains('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
            $this->assertContains('<strong>Preserve your Magento layout:</strong>', $catNavOrderingComment);
        }

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            // Maximum Number of Products Per Category
            $this->assertMatchesRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertMatchesRegularExpression(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertMatchesRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s',
                $responseBody
            );
        } else {
            // Maximum Number of Products Per Category
            $this->assertRegExp(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertRegExp(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertRegExp('#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s', $responseBody);
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/admin/url/use_custom 1
     * @magentoConfigFixture default_store admin/url/use_custom 1
     * @magentoConfigFixture default/admin/url/custom http://localhost/
     * @magentoConfigFixture default_store admin/url/custom http://localhost/
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 1
     * @magentoConfigFixture default/klevu_search/add_to_cart/enabledaddtocartfront 0
     * @magentoConfigFixture default_store klevu_search/add_to_cart/enabledaddtocartfront 0
     */
    public function testRender_StoreScope_CategoryNavigationUnavailable_PreserveLayoutUnavailable_DefaultConfig()
    {
        $this->setupPhp5();
        $defaultStore = $this->storeManager->getDefaultStoreView();

        $this->scopeConfigWriterMock->expects($this->never())
            ->method('save');

        $this->accountFeaturesMock->method('isFeatureAvailable')->willReturnCallback(
            static function ($feature, $strict = false) {
                switch ($feature) {
                    case AccountFeatures::PM_FEATUREFLAG_PRESERVES_LAYOUT:
                    case AccountFeatures::PM_FEATUREFLAG_CATEGORY_NAVIGATION:
                    default:
                        $return = false;
                        break;
                }

                return $return;
            }
        );

        $request = $this->getRequest();
        $request->setParam('store', $defaultStore->getId());
        $request->setParam('section', 'klevu_search');
        $request->setMethod('GET');

        $this->dispatch($this->getAdminFrontName() . '/admin/system_config/edit');

        $response = $this->getResponse();
        $httpResponseCode = $response->getHttpResponseCode();
        $this->assertNotSame(404, $httpResponseCode);
        $this->assertNotSame($this->expectedNoAccessResponseCode, $httpResponseCode);

        $responseBody = $response->getBody();

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        } else {
            $this->assertRegExp(
                '#<fieldset[^>]+id="klevu_search_categorylanding"#',
                $responseBody
            );
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        } else {
            $this->assertNotRegExp(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        }

        // Ordering and Rendering
        $matches = [];
        preg_match(
            '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation".*?</tr>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering row');
        $catNavOrderingRow = current($matches);
        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString('Switch to Store View scope to manage', $catNavOrderingRow);
        } else {
            $this->assertNotContains('Switch to Store View scope to manage', $catNavOrderingRow);
        }
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString(
                '<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>',
                $catNavOrderingRow
            );
        } else {
            $this->assertContains('<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>', $catNavOrderingRow);
        }

        $matches = [];
        preg_match(
            '#<select[^>]+id="klevu_search_categorylanding_enabledcategorynavigation".*?>.*?</select>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering field');
        $catNavOrderingField = current($matches);
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('disabled', $catNavOrderingField);
        } else {
            $this->assertContains('disabled', $catNavOrderingField);
        }
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertRegExp(
                '#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertRegExp(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s',
                $catNavOrderingField
            );
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertDoesNotMatchRegularExpression(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertNotRegExp(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertNotRegExp(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        }

        $matches = [];
        preg_match('#<p[^>]+class="note"[^>]*>.*?</p>#s', $catNavOrderingRow, $matches);
        $this->assertCount(1, $matches, 'CatNav Ordering comment');
        $catNavOrderingComment = current($matches);

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertStringContainsString('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
        } else {
            $this->assertContains('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertContains('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
        }
        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                '<strong>Preserve your Magento layout:</strong>',
                $catNavOrderingComment
            );
        } else {
            $this->assertNotContains('<strong>Preserve your Magento layout:</strong>', $catNavOrderingComment);
        }

        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            // Maximum Number of Products Per Category
            $this->assertDoesNotMatchRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertDoesNotMatchRegularExpression(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertDoesNotMatchRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s',
                $responseBody
            );
        } else {
            // Maximum Number of Products Per Category
            $this->assertNotRegExp(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertNotRegExp(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertNotRegExp(
                '#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s',
                $responseBody
            );
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/admin/url/use_custom 1
     * @magentoConfigFixture default_store admin/url/use_custom 1
     * @magentoConfigFixture default/admin/url/custom http://localhost/
     * @magentoConfigFixture default_store admin/url/custom http://localhost/
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default/klevu_search/add_to_cart/enabledaddtocartfront 0
     * @magentoConfigFixture default_store klevu_search/add_to_cart/enabledaddtocartfront 0
     */
    public function testRender_StoreScope_CategoryNavigationAvailable_PreserveLayoutUnavailable_PLSelectedInConfig()
    {
        $this->setupPhp5();
        $defaultStore = $this->storeManager->getDefaultStoreView();

        $this->scopeConfigWriterMock->expects($this->atLeastOnce())
            ->method('save')
            ->with(
                'klevu_search/categorylanding/enabledcategorynavigation',
                1,
                'stores',
                (int)$defaultStore->getId()
            );

        $this->accountFeaturesMock->method('isFeatureAvailable')->willReturnCallback(
            static function ($feature, $strict = false) {
                switch ($feature) {
                    case AccountFeatures::PM_FEATUREFLAG_CATEGORY_NAVIGATION:
                        $return = true;
                        break;

                    case AccountFeatures::PM_FEATUREFLAG_PRESERVES_LAYOUT:
                    default:
                        $return = false;
                        break;
                }

                return $return;
            }
        );

        $request = $this->getRequest();
        $request->setParam('store', $defaultStore->getId());
        $request->setParam('section', 'klevu_search');
        $request->setMethod('GET');

        $this->dispatch($this->getAdminFrontName() . '/admin/system_config/edit');

        $response = $this->getResponse();
        $httpResponseCode = $response->getHttpResponseCode();
        $this->assertNotSame(404, $httpResponseCode);
        $this->assertNotSame($this->expectedNoAccessResponseCode, $httpResponseCode);

        $responseBody = $response->getBody();

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        } else {
            $this->assertRegExp('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        } else {
            $this->assertNotRegExp(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        }

        // Ordering and Rendering
        $matches = [];
        preg_match(
            '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation".*?</tr>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering row');
        $catNavOrderingRow = current($matches);
        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString('Switch to Store View scope to manage', $catNavOrderingRow);
            $this->assertStringNotContainsString(
                '<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>',
                $catNavOrderingRow
            );
        } else {
            $this->assertNotContains('Switch to Store View scope to manage', $catNavOrderingRow);
            $this->assertNotContains('<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>', $catNavOrderingRow);
        }

        $matches = [];
        preg_match(
            '#<select[^>]+id="klevu_search_categorylanding_enabledcategorynavigation".*?>.*?</select>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering field');
        $catNavOrderingField = current($matches);
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertRegExp('#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s', $catNavOrderingField);
            $this->assertRegExp('#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s', $catNavOrderingField);
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertDoesNotMatchRegularExpression(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertNotRegExp(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertNotRegExp(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        }

        $matches = [];
        preg_match('#<p[^>]+class="note"[^>]*>.*?</p>#s', $catNavOrderingRow, $matches);
        $this->assertCount(1, $matches, 'CatNav Ordering comment');
        $catNavOrderingComment = current($matches);

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertStringContainsString('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
        } else {
            $this->assertContains('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertContains('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
        }

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                '<strong>Preserve your Magento layout:</strong>',
                $catNavOrderingComment
            );
        } else {
            $this->assertNotContains('<strong>Preserve your Magento layout:</strong>', $catNavOrderingComment);
        }

        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            // Maximum Number of Products Per Category
            $this->assertDoesNotMatchRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertDoesNotMatchRegularExpression(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertDoesNotMatchRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s',
                $responseBody
            );
        } else {
            // Maximum Number of Products Per Category
            $this->assertNotRegExp(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertNotRegExp(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertNotRegExp(
                '#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s',
                $responseBody
            );
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/admin/url/use_custom 1
     * @magentoConfigFixture default_store admin/url/use_custom 1
     * @magentoConfigFixture default/admin/url/custom http://localhost/
     * @magentoConfigFixture default_store admin/url/custom http://localhost/
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 3
     * @magentoConfigFixture default/klevu_search/add_to_cart/enabledaddtocartfront 0
     * @magentoConfigFixture default_store klevu_search/add_to_cart/enabledaddtocartfront 0
     */
    public function testRender_StoreScope_CategoryNavigationAvailable_PreserveLayoutUnavailable_ThemeSelectedInConfig()
    {
        $this->setupPhp5();
        $defaultStore = $this->storeManager->getDefaultStoreView();

        $this->scopeConfigWriterMock->expects($this->never())
            ->method('save');

        $this->accountFeaturesMock->method('isFeatureAvailable')->willReturnCallback(
            static function ($feature, $strict = false) {
                switch ($feature) {
                    case AccountFeatures::PM_FEATUREFLAG_CATEGORY_NAVIGATION:
                        $return = true;
                        break;

                    case AccountFeatures::PM_FEATUREFLAG_PRESERVES_LAYOUT:
                    default:
                        $return = false;
                        break;
                }

                return $return;
            }
        );

        $request = $this->getRequest();
        $request->setParam('store', $defaultStore->getId());
        $request->setParam('section', 'klevu_search');
        $request->setMethod('GET');

        $this->dispatch($this->getAdminFrontName() . '/admin/system_config/edit');

        $response = $this->getResponse();
        $httpResponseCode = $response->getHttpResponseCode();
        $this->assertNotSame(404, $httpResponseCode);
        $this->assertNotSame($this->expectedNoAccessResponseCode, $httpResponseCode);

        $responseBody = $response->getBody();

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        } else {
            $this->assertRegExp('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        } else {
            $this->assertNotRegExp(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        }

        // Ordering and Rendering
        $matches = [];
        preg_match(
            '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation".*?</tr>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering row');
        $catNavOrderingRow = current($matches);
        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString('Switch to Store View scope to manage', $catNavOrderingRow);
            $this->assertStringNotContainsString(
                '<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>',
                $catNavOrderingRow
            );
        } else {
            $this->assertNotContains('Switch to Store View scope to manage', $catNavOrderingRow);
            $this->assertNotContains('<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>', $catNavOrderingRow);
        }

        $matches = [];
        preg_match(
            '#<select[^>]+id="klevu_search_categorylanding_enabledcategorynavigation".*?>.*?</select>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering field');
        $catNavOrderingField = current($matches);
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="1".*?>\s*Native\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="3"[^>]+selected.*?>\s*Klevu JS Theme\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertRegExp('#<option[^>]+value="1".*?>\s*Native\s*</option>#s', $catNavOrderingField);
            $this->assertRegExp(
                '#<option[^>]+value="3"[^>]+selected.*?>\s*Klevu JS Theme\s*</option>#s',
                $catNavOrderingField
            );
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertDoesNotMatchRegularExpression(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertNotRegExp(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertNotRegExp(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        }

        $matches = [];
        preg_match('#<p[^>]+class="note"[^>]*>.*?</p>#s', $catNavOrderingRow, $matches);
        $this->assertCount(1, $matches, 'CatNav Ordering comment');
        $catNavOrderingComment = current($matches);

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertStringContainsString('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
        } else {
            $this->assertContains('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertContains('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
        }

        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                '<strong>Preserve your Magento layout:</strong>',
                $catNavOrderingComment
            );
        } else {
            $this->assertNotContains('<strong>Preserve your Magento layout:</strong>', $catNavOrderingComment);
        }

        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            // Maximum Number of Products Per Category
            $this->assertDoesNotMatchRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertDoesNotMatchRegularExpression(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertDoesNotMatchRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s',
                $responseBody
            );
        } else {
            // Maximum Number of Products Per Category
            $this->assertNotRegExp(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertNotRegExp(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertNotRegExp(
                '#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s',
                $responseBody
            );
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/admin/url/use_custom 1
     * @magentoConfigFixture default_store admin/url/use_custom 1
     * @magentoConfigFixture default/admin/url/custom http://localhost/
     * @magentoConfigFixture default_store admin/url/custom http://localhost/
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default/klevu_search/add_to_cart/enabledaddtocartfront 0
     * @magentoConfigFixture default_store klevu_search/add_to_cart/enabledaddtocartfront 0
     */
    public function testRender_StoreScope_CategoryNavigationUnavailable_PreserveLayoutAvailable_PLSelectedInConfig()
    {
        $this->setupPhp5();
        $defaultStore = $this->storeManager->getDefaultStoreView();

        $this->scopeConfigWriterMock->expects($this->atLeastOnce())
            ->method('save')
            ->with(
                'klevu_search/categorylanding/enabledcategorynavigation',
                1,
                'stores',
                (int)$defaultStore->getId()
            );

        $this->accountFeaturesMock->method('isFeatureAvailable')->willReturnCallback(
            static function ($feature, $strict = false) {
                switch ($feature) {
                    case AccountFeatures::PM_FEATUREFLAG_PRESERVES_LAYOUT:
                        $return = true;
                        break;

                    case AccountFeatures::PM_FEATUREFLAG_CATEGORY_NAVIGATION:
                    default:
                        $return = false;
                        break;
                }

                return $return;
            }
        );

        $request = $this->getRequest();
        $request->setParam('store', $defaultStore->getId());
        $request->setParam('section', 'klevu_search');
        $request->setMethod('GET');

        $this->dispatch($this->getAdminFrontName() . '/admin/system_config/edit');

        $response = $this->getResponse();
        $httpResponseCode = $response->getHttpResponseCode();
        $this->assertNotSame(404, $httpResponseCode);
        $this->assertNotSame($this->expectedNoAccessResponseCode, $httpResponseCode);

        $responseBody = $response->getBody();

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        } else {
            $this->assertRegExp('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        } else {
            $this->assertNotRegExp(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        }

        // Ordering and Rendering
        $matches = [];
        preg_match(
            '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation".*?</tr>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering row');
        $catNavOrderingRow = current($matches);
        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString('Switch to Store View scope to manage', $catNavOrderingRow);
        } else {
            $this->assertNotContains('Switch to Store View scope to manage', $catNavOrderingRow);
        }
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString(
                '<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>',
                $catNavOrderingRow
            );
        } else {
            $this->assertContains('<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>', $catNavOrderingRow);
        }

        $matches = [];
        preg_match(
            '#<select[^>]+id="klevu_search_categorylanding_enabledcategorynavigation".*?>.*?</select>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering field');
        $catNavOrderingField = current($matches);
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('disabled', $catNavOrderingField);
        } else {
            $this->assertContains('disabled', $catNavOrderingField);
        }
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertRegExp(
                '#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertRegExp(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertRegExp(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertNotRegExp(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s',
                $catNavOrderingField
            );
        }

        $matches = [];
        preg_match('#<p[^>]+class="note"[^>]*>.*?</p>#s', $catNavOrderingRow, $matches);
        $this->assertCount(1, $matches, 'CatNav Ordering comment');
        $catNavOrderingComment = current($matches);

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertStringContainsString('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
            $this->assertStringContainsString('<strong>Preserve your Magento layout:</strong>', $catNavOrderingComment);
        } else {
            $this->assertContains('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertContains('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
            $this->assertContains('<strong>Preserve your Magento layout:</strong>', $catNavOrderingComment);
        }

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            // Maximum Number of Products Per Category
            $this->assertMatchesRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertMatchesRegularExpression(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertMatchesRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s',
                $responseBody
            );
        } else {
            // Maximum Number of Products Per Category
            $this->assertRegExp(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertRegExp(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertRegExp('#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s', $responseBody);
        }
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture default/admin/url/use_custom 1
     * @magentoConfigFixture default_store admin/url/use_custom 1
     * @magentoConfigFixture default/admin/url/custom http://localhost/
     * @magentoConfigFixture default_store admin/url/custom http://localhost/
     * @magentoConfigFixture default/klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default_store klevu_search/categorylanding/enabledcategorynavigation 2
     * @magentoConfigFixture default/klevu_search/add_to_cart/enabledaddtocartfront 0
     * @magentoConfigFixture default_store klevu_search/add_to_cart/enabledaddtocartfront 0
     */
    public function testRender_StoreScope_CategoryNavigationUnavailable_PreserveLayoutUnavailable_ThemeSelectedInConfig() // phpcs:ignore Generic.Files.LineLength.TooLong
    {
        $this->setupPhp5();
        $defaultStore = $this->storeManager->getDefaultStoreView();

        $this->scopeConfigWriterMock->expects($this->atLeastOnce())
            ->method('save')
            ->with(
                'klevu_search/categorylanding/enabledcategorynavigation',
                1,
                'stores',
                (int)$defaultStore->getId()
            );

        $this->accountFeaturesMock->method('isFeatureAvailable')->willReturnCallback(
            static function ($feature, $strict = false) {
                switch ($feature) {
                    case AccountFeatures::PM_FEATUREFLAG_PRESERVES_LAYOUT:
                    case AccountFeatures::PM_FEATUREFLAG_CATEGORY_NAVIGATION:
                    default:
                        $return = false;
                        break;
                }

                return $return;
            }
        );

        $request = $this->getRequest();
        $request->setParam('store', $defaultStore->getId());
        $request->setParam('section', 'klevu_search');
        $request->setMethod('GET');

        $this->dispatch($this->getAdminFrontName() . '/admin/system_config/edit');

        $response = $this->getResponse();
        $httpResponseCode = $response->getHttpResponseCode();
        $this->assertNotSame(404, $httpResponseCode);
        $this->assertNotSame($this->expectedNoAccessResponseCode, $httpResponseCode);

        $responseBody = $response->getBody();

        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        } else {
            $this->assertRegExp('#<fieldset[^>]+id="klevu_search_categorylanding"#', $responseBody);
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        } else {
            $this->assertNotRegExp(
                '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation_info".*?</tr>#s',
                $responseBody
            );
        }

        // Ordering and Rendering
        $matches = [];
        preg_match(
            '#<tr[^>]+id="row_klevu_search_categorylanding_enabledcategorynavigation".*?</tr>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering row');
        $catNavOrderingRow = current($matches);
        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString('Switch to Store View scope to manage', $catNavOrderingRow);
        } else {
            $this->assertNotContains('Switch to Store View scope to manage', $catNavOrderingRow);
        }
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString(
                '<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>',
                $catNavOrderingRow
            );
        } else {
            $this->assertContains('<div class="klevu-upgrade-block">TEST UPGRADE MESSAGE</div>', $catNavOrderingRow);
        }

        $matches = [];
        preg_match(
            '#<select[^>]+id="klevu_search_categorylanding_enabledcategorynavigation".*?>.*?</select>#s',
            $responseBody,
            $matches
        );
        $this->assertCount(1, $matches, 'CatNav Ordering field');
        $catNavOrderingField = current($matches);
        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('disabled', $catNavOrderingField);
        } else {
            $this->assertContains('disabled', $catNavOrderingField);
        }
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertMatchesRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertRegExp('#<option[^>]+value="1"[^>]+selected.*?>\s*Native\s*</option>#s', $catNavOrderingField);
            $this->assertRegExp('#<option[^>]+value="3".*?>\s*Klevu JS Theme\s*</option>#s', $catNavOrderingField);
        }
        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            $this->assertDoesNotMatchRegularExpression(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertDoesNotMatchRegularExpression(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        } else {
            $this->assertNotRegExp(
                '#<option[^>]+value="3".*?>\s*Klevu JS Theme \(Recommended\)\s*</option>#s',
                $catNavOrderingField
            );
            $this->assertNotRegExp(
                '#<option[^>]+value="2".*?>\s*Preserve your Magento layout\s*</option>#s',
                $catNavOrderingField
            );
        }

        $matches = [];
        preg_match('#<p[^>]+class="note"[^>]*>.*?</p>#s', $catNavOrderingRow, $matches);
        $this->assertCount(1, $matches, 'CatNav Ordering comment');
        $catNavOrderingComment = current($matches);

        if (method_exists($this, 'assertStringContainsString')) {
            $this->assertStringContainsString('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertStringContainsString('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
        } else {
            $this->assertContains('<strong>Native:</strong>', $catNavOrderingComment);
            $this->assertContains('<strong>Klevu JS Theme:</strong>', $catNavOrderingComment);
        }
        if (method_exists($this, 'assertStringNotContainsString')) {
            $this->assertStringNotContainsString(
                '<strong>Preserve your Magento layout:</strong>',
                $catNavOrderingComment
            );
        } else {
            $this->assertNotContains('<strong>Preserve your Magento layout:</strong>', $catNavOrderingComment);
        }

        if (method_exists($this, 'assertDoesNotMatchRegularExpression')) {
            // Maximum Number of Products Per Category
            $this->assertDoesNotMatchRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertDoesNotMatchRegularExpression(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertDoesNotMatchRegularExpression(
                '#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s',
                $responseBody
            );
        } else {
            // Maximum Number of Products Per Category
            $this->assertNotRegExp(
                '#<input[^>]+id="klevu_search_categorylanding_max_no_of_products".*?/>#s',
                $responseBody
            );

            // Sort by Klevu Relevance
            $this->assertNotRegExp(
                '#<select[^>]+id="klevu_search_categorylanding_klevu_cat_relevance".*?>.*?</select>#s',
                $responseBody
            );

            // Relevance Label
            $this->assertNotRegExp(
                '#<input[^>]+id="klevu_search_categorylanding_relevance_label".*?/>#s',
                $responseBody
            );
        }
    }

    /**
     * Alternative setup method to accommodate lack of return type casting in PHP5.6,
     *  given setUp() requires a void return type
     *
     * @return void
     * @throws AuthenticationException
     * @todo Move to setUp when PHP 5.x is no longer supported
     */
    private function setupPhp5()
    {
        $this->setUp();

        $this->storeManager = $this->_objectManager->get(StoreManagerInterface::class);

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->_objectManager->addSharedInstance($this->loggerMock, 'Klevu\Search\Logger\Logger\Search');

        $this->scopeConfigWriterMock = $this->getMockBuilder(ScopeConfigWriter::class)
            ->disableOriginalConstructor()
            ->setMethods(['save'])
            ->getMock();
        $this->_objectManager->addSharedInstance($this->scopeConfigWriterMock, ScopeConfigWriterInterface::class);
        $this->_objectManager->addSharedInstance($this->scopeConfigWriterMock, ScopeConfigWriter::class);

        $this->accountFeaturesMock = $this->getMockBuilder(AccountFeatures::class)
            ->disableOriginalConstructor()
            ->setMethods(['isFeatureAvailable', 'getUpgradeMessage'])
            ->getMock();
        $this->accountFeaturesMock->method('getUpgradeMessage')
            ->willReturn('TEST UPGRADE MESSAGE');

        $this->getFeaturesMock = $this->getMockBuilder(GetFeaturesInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();
        $this->getFeaturesMock->method('execute')->willReturn($this->accountFeaturesMock);

        $this->_objectManager->addSharedInstance($this->getFeaturesMock, GetFeaturesInterface::class);
        $this->_objectManager->addSharedInstance($this->getFeaturesMock, GetFeatures::class);

        $this->uri = $this->getAdminFrontName() . '/admin/system_config/edit/section/klevu_search';
    }

    /**
     * @inheritdoc
     */
    public function testAclHasAccess()
    {
        $this->setupPhp5();

        if ($this->uri === null) {
            $this->markTestIncomplete('AclHasAccess test is not complete');
        }
        if ($this->httpMethod) {
            $this->getRequest()->setMethod($this->httpMethod);
        }
        $this->dispatch($this->uri);
        $this->assertNotSame(404, $this->getResponse()->getHttpResponseCode());
        $this->assertNotSame($this->expectedNoAccessResponseCode, $this->getResponse()->getHttpResponseCode());
    }

    /**
     * @inheritdoc
     */
    public function testAclNoAccess()
    {
        $this->setupPhp5();
        if ($this->resource === null || $this->uri === null) {
            $this->markTestIncomplete('Acl test is not complete');
        }
        if ($this->httpMethod) {
            $this->getRequest()->setMethod($this->httpMethod);
        }
        $this->_objectManager->get(\Magento\Framework\Acl\Builder::class)
            ->getAcl()
            ->deny($this->_auth->getUser()->getRoles(), $this->resource);
        $this->dispatch($this->uri);
        $this->assertSame($this->expectedNoAccessResponseCode, $this->getResponse()->getHttpResponseCode());
    }

    /**
     * Returns configured admin front name for use in dispatching controller requests
     *
     * @return string
     */
    private function getAdminFrontName()
    {
        /** @var AreaList $areaList */
        $areaList = $this->_objectManager->get(AreaList::class);
        $adminFrontName = $areaList->getFrontName('adminhtml');
        if (!$adminFrontName) {
            /** @var FrontNameResolver $backendFrontNameResolver */
            $backendFrontNameResolver = $this->_objectManager->get(FrontNameResolver::class);
            $adminFrontName = $backendFrontNameResolver->getFrontName(true);
        }

        return (string)$adminFrontName;
    }
}
