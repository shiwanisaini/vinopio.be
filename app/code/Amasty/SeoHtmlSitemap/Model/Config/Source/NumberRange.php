<?php

namespace Amasty\SeoHtmlSitemap\Model\Config\Source;

class NumberRange implements \Magento\Framework\Option\ArrayInterface
{
    protected $_rangeMin = 1;

    protected $_rangeMax = 5;

    public function toOptionArray()
    {
        $data = [];
        for ($i = $this->_rangeMin; $i <= $this->_rangeMax; $i++) {
            $data[] = ['value' => $i, 'label' => $i];
        }

        return $data;
    }
}