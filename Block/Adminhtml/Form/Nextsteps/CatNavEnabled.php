<?php

namespace Klevu\Categorynavigation\Block\Adminhtml\Form\Nextsteps;

use Klevu\Search\Api\Service\Account\GetFeaturesInterface;
use Klevu\Search\Service\Account\Model\AccountFeatures;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class CatNavEnabled extends Field
{
    /**
     * @var GetFeaturesInterface
     */
    private $getFeatures;

    public function __construct(
        Context $context,
        GetFeaturesInterface $getFeatures,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->getFeatures = $getFeatures;
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $account = $this->getFeatures->execute();
        if (!$account->isFeatureEnabled(AccountFeatures::PM_FEATUREFLAG_CATEGORY_NAVIGATION)) {
            return '';
        }

        return parent::render($element);
    }
}
