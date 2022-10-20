<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Shop by Seo for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\ShopbySeo\Model\Source;

class SeoByDefault implements \Magento\Framework\Data\OptionSourceInterface
{
    public const GENERATED = 1;
    public const NOT_GENERATED = 0;

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::GENERATED,
                'label' => __('Generated')
            ],
            [
                'value' => self::NOT_GENERATED,
                'label' => __('Not Generated')
            ],
        ];
    }
}
