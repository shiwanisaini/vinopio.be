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



namespace Mirasvit\QuickNavigation;

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Store\Model\StoreManagerInterface;

class Context
{
    private $layerResolver;

    private $storeManager;


    public function __construct(
        LayerResolver $layerResolver,
        StoreManagerInterface $storeManager
    ) {
        $this->layerResolver = $layerResolver;
        $this->storeManager  = $storeManager;
    }

    public function getStoreId()
    {
        return (int)$this->storeManager->getStore()->getId();
    }

    public function getCategoryId()
    {
        $category = $this->layerResolver->get()->getCurrentCategory();

        return $category
            ? (int)$category->getId()
            : 0;
    }

    /**
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        return $this->layerResolver->get();
    }

    /**
     * @return \Magento\Catalog\Model\Layer\State
     */
    public function getState()
    {
        return $this->getLayer()->getState();
    }

    /**
     * @return string
     */
    public function getSequenceString()
    {
        $filterList = [];
        foreach ($this->getState()->getFilters() as $filter) {
            foreach (explode(',', $filter->getValueString()) as $value) {
                $filterList[] = $filter->getFilter()->getRequestVar() . ':' . $value;
            }
        }

        return implode('|', $filterList);
    }

    /**
     * @return int
     */
    public function getSequenceLength()
    {
        return count($this->getState()->getFilters());
    }
}
