<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Shop by Base for Magento 2 (System)
*/

namespace Amasty\ShopbyBase\Model\ResourceModel\FilterSetting;

/**
 * FilterSetting Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Collection protected constructor
     */
    protected function _construct()
    {
        $this->_init(
            \Amasty\ShopbyBase\Model\FilterSetting::class,
            \Amasty\ShopbyBase\Model\ResourceModel\FilterSetting::class
        );
    }
}
