<?php

namespace Klevu\Categorynavigation\Model\System\Config\Source;

use Klevu\Search\Api\Service\Account\GetFeaturesInterface;
use Klevu\Search\Model\Product\Sync as Klevu_ProductSync;
use Klevu\Search\Service\Account\Model\AccountFeatures;
use Magento\Framework\App\ObjectManager;

class Categorylandingoptions
{
    const KLEVU_PRESERVE_LAYOUT    = 2;
    const MAGENTO_DEFAULT     = 1;
    const KLEVU_TEMPLATE_LAYOUT = 3;

    /**
     * @var Klevu_ProductSync
     * @deprecated
     */
    protected $_klevuProductSync;

    /**
     * @var GetFeaturesInterface|mixed
     */
    private $getFeatures;

    /**
     * @var array[]
     */
    private $options;

    /**
     * @param Klevu_ProductSync $klevuProductSync Deprecated
     * @param GetFeaturesInterface|null $getFeatures
     */
    public function __construct(
        Klevu_ProductSync $klevuProductSync,
        GetFeaturesInterface $getFeatures = null
    ) {
        $this->_klevuProductSync = $klevuProductSync;
        $this->getFeatures = $getFeatures ?: ObjectManager::getInstance()->get(GetFeaturesInterface::class);
    }

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $accountFeatures = $this->getFeatures->execute();

            $this->options = [
                ['value' => static::MAGENTO_DEFAULT, 'label' => __("Native")],
            ];

            if ($accountFeatures && $accountFeatures->isFeatureAvailable(AccountFeatures::PM_FEATUREFLAG_PRESERVES_LAYOUT)) {
                $this->options[] = ['value' => static::KLEVU_TEMPLATE_LAYOUT, 'label' => __("Klevu JS Theme (Recommended)")];
                $this->options[] = ['value' => static::KLEVU_PRESERVE_LAYOUT, 'label' => __("Preserve your Magento layout")];
            } else {
                $this->options[] = ['value' => static::KLEVU_TEMPLATE_LAYOUT, 'label' => __("Klevu JS Theme")];
            }
        }

        return $this->options;
    }
}
