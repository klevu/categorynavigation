<?php

namespace Klevu\Categorynavigation\Model\System\Config\Source;

use Klevu\Search\Model\Product\Sync as Klevu_ProductSync;

class Categorylandingoptions
{

    const KLEVU_PRESERVE_LAYOUT    = 2;
    const MAGENTO_DEFAULT     = 1;
    const KLEVU_TEMPLATE_LAYOUT = 3;

    public function __construct(
        Klevu_ProductSync $klevuProductSync
    )
    {
        $this->_klevuProductSync = $klevuProductSync;
    }

    public function toOptionArray()
    {
        $check_preserve = $this->_klevuProductSync->getFeatures();
        if (!empty($check_preserve['disabled'])) {
            if (strpos($check_preserve['disabled'],"preserves_layout") !== false) {
                return [
                    ['value' => static::MAGENTO_DEFAULT, 'label' => __("Native")],
                    ['value' => static::KLEVU_TEMPLATE_LAYOUT, 'label' => __("Klevu JS Theme (Recommended)")],
                ];
            } else {
                return [
                    ['value' => static::MAGENTO_DEFAULT, 'label' => __("Native")],
                    ['value' => static::KLEVU_TEMPLATE_LAYOUT, 'label' => __("Klevu JS Theme (Recommended)")],
                    ['value' => static::KLEVU_PRESERVE_LAYOUT, 'label' => __("Preserve your Magento layout")],
                ];
            }
        } else if (empty($check_preserve['disabled'])) {
            return [
                ['value' => static::MAGENTO_DEFAULT, 'label' => __("Native")],
                ['value' => static::KLEVU_TEMPLATE_LAYOUT, 'label' => __("Klevu JS Theme (Recommended)")],
                ['value' => static::KLEVU_PRESERVE_LAYOUT, 'label' => __("Preserve your Magento layout")],
            ];
        } else {
            return [
                ['value' => static::MAGENTO_DEFAULT, 'label' => __("Native")],
                ['value' => static::KLEVU_TEMPLATE_LAYOUT, 'label' => __("Klevu JS Theme (Recommended)")],
            ];
        }
    }
}
