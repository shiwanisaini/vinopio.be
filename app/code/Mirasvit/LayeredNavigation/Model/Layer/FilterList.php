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



namespace Mirasvit\LayeredNavigation\Model\Layer;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\LayeredNavigation\Api\Data\AttributeConfigInterface;
use Mirasvit\LayeredNavigation\Model\Config\ExtraFiltersConfig;
use Mirasvit\LayeredNavigation\Model\Config\HorizontalBarConfig;
use Mirasvit\LayeredNavigation\Model\Config\Source\HorizontalFilterOptions;
use Mirasvit\LayeredNavigation\Repository\AttributeConfigRepository;

class FilterList extends Layer\FilterList
{
    /**
     * @var bool
     */
    protected $isHorizontal;

    protected $filterTypes
        = [
            self::CATEGORY_FILTER  => Filter\Category::class,
            self::ATTRIBUTE_FILTER => Filter\Attribute::class,
            self::PRICE_FILTER     => Filter\Price::class,
            self::DECIMAL_FILTER   => Filter\Decimal::class,
        ];

    /**
     * @var array
     */
    private $additionalFilters;

    private $attributeConfigRepository;

    /**
     * @var ExtraFiltersConfig
     */
    private $additionalFiltersConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var HorizontalBarConfig
     */
    private $horizontalFiltersConfig;

    public function __construct(
        ObjectManagerInterface $objectManager,
        FilterableAttributeListInterface $filterableAttributes,
        ExtraFiltersConfig $additionalFiltersConfig,
        StoreManagerInterface $storeManager,
        HorizontalBarConfig $horizontalFiltersConfig,
        AttributeConfigRepository $attributeConfigRepository,
        $isHorizontal = false,
        array $filters = [],
        array $additionalFilters = []
    ) {
        $this->isHorizontal              = $isHorizontal;
        $this->additionalFiltersConfig   = $additionalFiltersConfig;
        $this->storeManager              = $storeManager;
        $this->horizontalFiltersConfig   = $horizontalFiltersConfig;
        $this->additionalFilters         = $additionalFilters;
        $this->attributeConfigRepository = $attributeConfigRepository;

        if (!CompatibilityService::is24()) {
            call_user_func_array('parent::__construct', [$objectManager, $filterableAttributes, $filters]);
        } else {
            $layerCategoryConfig = CompatibilityService::getObjectManager()
                ->create(\Magento\Catalog\Model\Config\LayerCategoryConfig::class);

            call_user_func_array('parent::__construct', [$objectManager, $filterableAttributes, $layerCategoryConfig, $filters]);
        }
    }

    /**
     * Retrieve list of filters
     *@SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @param Layer $layer
     *
     * @return array|AbstractFilter[]
     */
    public function getFilters(Layer $layer)
    {
        if (!count($this->filters)) {
            $this->filters = [
                $this->objectManager->create($this->filterTypes[self::CATEGORY_FILTER], ['layer' => $layer]),
            ];

            foreach ($this->filterableAttributes->getList() as $attribute) {
                $this->filters[] = $this->createAttributeFilter($attribute, $layer);
            }

            foreach ($this->filters as $key => $filter) {
                $attribute = $filter->getData('attribute_model');

                if (!$attribute) {
                    continue;
                }

                $attributeConfig = $this->attributeConfigRepository->getByAttributeCode($attribute->getAttributeCode());

                if (!$attributeConfig) {
                    continue;
                }

                if ($attributeConfig->getCategoryVisibilityMode() == AttributeConfigInterface::CATEGORY_VISIBILITY_MODE_SHOW_IN_SELECTED
                    && !in_array($layer->getCurrentCategory()->getId(), $attributeConfig->getCategoryVisibilityIds())) {
                    unset($this->filters[$key]);
                }

                if ($attributeConfig->getCategoryVisibilityMode() == AttributeConfigInterface::CATEGORY_VISIBILITY_MODE_HIDE_IN_SELECTED
                    && in_array($layer->getCurrentCategory()->getId(), $attributeConfig->getCategoryVisibilityIds())) {
                    unset($this->filters[$key]);
                }
            }

            $this->applyFilterPosition($this->getAdditionalFilters($layer));

            if ($this->isHorizontal) {
                $this->deleteIgnoredFilter();
            } else {
                $this->deleteHorizontalFilter();
            }
        }

        return $this->filters;
    }

    /**
     * @param array $additionalFilters
     *
     * @return bool
     */
    private function applyFilterPosition($additionalFilters)
    {
        if (!$additionalFilters) {
            return true;
        }

        foreach ($additionalFilters as $data) {
            foreach ($data as $position => $additionalFilter) {
                if (isset($this->filters[$position]) && $position != 0) {
                    $firstFilterPart  = array_slice($this->filters, 0, $position);
                    $secondFilterPart = array_slice($this->filters, $position);
                    $this->filters    = array_merge($firstFilterPart, [$additionalFilter], $secondFilterPart);
                } elseif ($position == 0) {
                    array_unshift($this->filters, $additionalFilter);
                } else {
                    $this->filters = array_merge($this->filters, [$additionalFilter]);
                }
            }
        }

        return true;
    }

    /**
     * @param \Magento\Catalog\Model\Layer $layer
     *
     * @return AbstractFilter[]
     */
    private function getAdditionalFilters($layer)
    {
        $additionalFilters = [];
        $storeId           = $this->storeManager->getStore()->getStoreId();

        foreach ($this->additionalFilters as $filter => $class) {
            if ($this->additionalFiltersConfig->isFilterEnabled($filter, $storeId)) {
                $position            = $this->additionalFiltersConfig->getFilterPosition($filter, $storeId);
                $additionalFilters[] = [
                    $position => $this->objectManager->create($class, ['layer' => $layer]),
                ];
            }
        }

        return $additionalFilters;
    }

    /**
     * @return bool
     */
    private function deleteIgnoredFilter()
    {
        $horizontalFiltersConfig = $this->horizontalFiltersConfig->getFilters(
            $this->storeManager->getStore()->getId()
        );

        if ($horizontalFiltersConfig == HorizontalFilterOptions::ALL_FILTERED_ATTRIBUTES) {
            return true;
        }

        if (!$horizontalFiltersConfig) {
            $this->filters = [];
        }

        if ($horizontalFiltersConfig) {
            foreach ($this->filters as $key => $filter) {
                if (array_search($filter->getRequestVar(), $horizontalFiltersConfig) === false) {
                    unset($this->filters[$key]);
                }
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    private function deleteHorizontalFilter()
    {
        $horizontalFiltersConfig = $this->horizontalFiltersConfig->getFilters(
            $this->storeManager->getStore()->getId()
        );

        if (!$horizontalFiltersConfig) {
            return true;
        }

        if ($horizontalFiltersConfig == HorizontalFilterOptions::ALL_FILTERED_ATTRIBUTES) {
            $this->filters = [];
        }

        if ($horizontalFiltersConfig) {
            foreach ($this->filters as $key => $filter) {
                if (array_search($filter->getRequestVar(), $horizontalFiltersConfig) !== false) {
                    unset($this->filters[$key]);
                }
            }
        }

        return true;
    }
}
