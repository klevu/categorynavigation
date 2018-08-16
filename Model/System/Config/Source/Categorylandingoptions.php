<?php

namespace Klevu\Categorynavigation\Model\System\Config\Source;

class Categorylandingoptions
{

    const KLEVU_PRESERVE_LAYOUT    = 2;
    const MAGENTO_DEFAULT     = 1;
    const KLEVU_TEMPLATE_LAYOUT = 3;
    
    public function toOptionArray()
    {
        $check_preserve = \Magento\Framework\App\ObjectManager::getInstance()->get('Klevu\Search\Model\Product\Sync')->getFeatures();
        if(!empty($check_preserve['disabled'])) {
            if(strpos($check_preserve['disabled'],"preserves_layout") !== false) {
                return array(
                    array('value' => static::MAGENTO_DEFAULT, 'label' => __("Magento's Default")),
                    array('value' => static::KLEVU_TEMPLATE_LAYOUT, 'label' => __("Klevu Powered - Based On Klevu Template")),
                );
            } else {
                return array(
                        array('value' => static::MAGENTO_DEFAULT, 'label' => __("Magento's Default")),
                        array('value' => static::KLEVU_PRESERVE_LAYOUT, 'label' => __("Klevu Powered - Preserve Theme Layout")),
                        array('value' => static::KLEVU_TEMPLATE_LAYOUT, 'label' => __("Klevu Powered - Based On Klevu Template"))
                );
            }
        } else if(empty($check_preserve['disabled'])){
                return array(
                        array('value' => static::MAGENTO_DEFAULT, 'label' => __("Magento's Default")),
                        array('value' => static::KLEVU_PRESERVE_LAYOUT, 'label' => __("Klevu Powered - Preserve Theme Layout")),
                        array('value' => static::KLEVU_TEMPLATE_LAYOUT, 'label' => __("Klevu Powered - Based On Klevu Template"))
                        
                );
        } else {
                return array(
                    array('value' => static::NO, 'label' => __("Magento's Default")),
                    array('value' => static::CATEGORYLAND, 'label' => __("Klevu Powered - Based On Klevu Template")),
                );
        }
    }
}
