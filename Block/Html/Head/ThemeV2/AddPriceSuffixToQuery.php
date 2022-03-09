<?php

namespace Klevu\Categorynavigation\Block\Html\Head\ThemeV2;

use Klevu\FrontendJs\Block\Template as FrontendJsTemplate;
use Klevu\FrontendJs\Constants as FrontendJsConstants;

/**
 * @todo Use ViewModels when older Magento BC support dropped
 */
class AddPriceSuffixToQuery extends FrontendJsTemplate
{
    /**
     * @return string
     */
    public function getCustomerDataLoadedEventName()
    {
        return FrontendJsConstants::JS_EVENTNAME_CUSTOMER_DATA_LOADED;
    }

    /**
     * @return string;
     */
    public function getCustomerDataLoadErrorEventName()
    {
        return FrontendJsConstants::JS_EVENTNAME_CUSTOMER_DATA_LOAD_ERROR;
    }
}
