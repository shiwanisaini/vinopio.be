<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Improved Layered Navigation Base for Magento 2
*/

namespace Amasty\Shopby\Model\ResourceModel\Catalog\Category;

use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;

class Collection extends \Magento\Catalog\Model\ResourceModel\Category\Collection
{
    /**
     * Remove "{{table}}.is_autogenerated = 1" in comparison with parent class.
     *
     * @return $this
     */
    public function joinUrlRewrite()
    {
        $this->joinTable(
            'url_rewrite',
            'entity_id = entity_id',
            ['request_path'],
            sprintf(
                '{{table}}.store_id = %d AND {{table}}.entity_type = \'%s\'',
                $this->_storeManager->getStore()->getId(),
                CategoryUrlRewriteGenerator::ENTITY_TYPE
            ),
            'left'
        );
        return $this;
    }
}
