<?php

namespace Klevu\Categorynavigation\Model\Context;

use Klevu\Search\Model\Context\Extended as CoreExtendedContext;
use Klevu\Categorynavigation\Helper\Config as CatnavHelperConfig;
use Klevu\Categorynavigation\Helper\Data as CatnavHelperData;
use Klevu\Categorynavigation\Model\Api\Magento\Request\CategoryInterface as ApiCategoryInterface;


class Extended extends CoreExtendedContext
{
    /**
     *  context constructor.
     * @param array $data
     */
    public function __construct(
        CatnavHelperConfig $CatnavHelperConfig,
        CatnavHelperData $CatnavHelperData,
        ApiCategoryInterface $CatnavIdsearch,
        $data = []

    )
    {
        $data = array(
            'catnav_config_helper' => $CatnavHelperConfig,
            'catnav_data_helper' => $CatnavHelperData,
            'klevu_catnav_id' => $CatnavIdsearch,
            'klevu_data' => $data
        );
        parent::__construct($data);
    }

    public function processOverrides(&$data){
        $data['helper_manager']->setData('catnav_config_helper',$this->getData("catnav_config_helper"));
        $data['helper_manager']->setData('catnav_data_helper',$this->getData("catnav_data_helper"));
        $data['klevu_context_api']->getData("klevu_core_api")->setData("klevu_catnav_id",$this->getData("klevu_catnav_id"));

        return $this;
    }


}
