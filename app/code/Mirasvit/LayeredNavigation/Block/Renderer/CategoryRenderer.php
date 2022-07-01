<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-navigation
 * @version   1.1.2
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\LayeredNavigation\Block\Renderer;

use Magento\Catalog\Model\Layer\Filter\Item;

class CategoryRenderer extends LabelRenderer
{
    protected $_template = 'Mirasvit_LayeredNavigation::renderer/categoryRenderer.phtml';

    public function getFilterItems()
    {
        return $this->sortFilterItems(parent::getFilterItems(), 0, 0);
    }

    /**
     * @param Item[] $items
     * @param int    $parentId
     * @param int    $level
     *
     * @return Item[]
     */
    private function sortFilterItems($items, $parentId, $level)
    {
        $result = [];

        foreach ($items as $item) {
            $itemId       = $item->getData('category_id');
            $itemParentId = explode('.', $item->getData('parent_id'))[1];
            $itemLevel    = $item->getData('level');

            if ($itemLevel !== $level) {
                continue;
            }

            if ($itemParentId != $parentId && $parentId !== 0) {
                continue;
            }

            $subItems = $this->sortFilterItems($items, $itemId, $level + 1);

            if (count($subItems)) {
                $item->setData('is_parent', true);
            }

            $result[] = $item;

            foreach ($subItems as $subItem) {
                $result[] = $subItem;
            }
        }

        return $result;
    }
}
