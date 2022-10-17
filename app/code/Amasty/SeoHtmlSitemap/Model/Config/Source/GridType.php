<?php

namespace Amasty\SeoHtmlSitemap\Model\Config\Source;

class GridType implements \Magento\Framework\Option\ArrayInterface
{
    public const TYPE_TREE = 1;
    public const TYPE_LIST = 2;

    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_TREE, 'label' => __('Tree')],
            ['value' => self::TYPE_LIST, 'label' => __('List')]
        ];
    }
}
