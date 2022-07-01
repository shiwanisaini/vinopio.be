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



namespace Mirasvit\LayeredNavigation\Model\Layer\Filter;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\LayeredNavigation\Model\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Model\Config\ExtraFiltersConfig;
use Mirasvit\LayeredNavigation\Model\Config\StateBarConfig;
use Mirasvit\LayeredNavigation\Service\ElasticsearchService;
use Mirasvit\LayeredNavigation\Service\Filter\FilterStockService;
use Mirasvit\LayeredNavigation\Service\FilterDataService;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StockFilter extends AbstractFilter
{
    use ConfigTrait;

    /**
     * @var array
     */
    protected static $isStateAdded = [];

    /**
     * @var string
     */
    protected $attributeCode = ExtraFiltersConfig::STOCK_FILTER;

    /**
     * @var bool
     */
    protected $isAdded = false;

    private          $filterDataService;

    private          $filterStockService;

    private          $additionalFiltersConfig;

    private          $storeManager;

    private          $filterClearBlockConfig;

    private          $elasticSearchService;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        DataBuilder $itemDataBuilder,
        FilterDataService $filterDataService,
        FilterStockService $filterStockService,
        ExtraFiltersConfig $additionalFiltersConfig,
        StateBarConfig $filterClearBlockConfig,
        ElasticsearchService $elasticSearchService,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->_requestVar             = ExtraFiltersConfig::STOCK_FILTER_FRONT_PARAM;
        $this->filterDataService       = $filterDataService;
        $this->filterStockService      = $filterStockService;
        $this->additionalFiltersConfig = $additionalFiltersConfig;
        $this->storeManager            = $storeManager;
        $this->filterClearBlockConfig  = $filterClearBlockConfig;
        $this->elasticSearchService    = $elasticSearchService;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->additionalFiltersConfig->isStockFilterEnabled($this->storeManager->getStore()->getStoreId())) {
            return $this;
        }

        $filter = $request->getParam(ExtraFiltersConfig::STOCK_FILTER_FRONT_PARAM);

        $filterPrepared = false;
        if ($filter && strpos($filter, ',') !== false) {
            $filterPrepared = explode(',', $filter);
        }

        if ($filter && $filterPrepared) {
            $this->addState(false, $filterPrepared);
            $this->isAdded = true;
        } elseif ($filter) {
            $productCollection = $this->getLayer()->getProductCollection();

            $filterValue = ($filter == ExtraFiltersConfig::IN_STOCK_FILTER) ? 1 : 0;
            if ($this->elasticSearchService->isElasticEngineUsed()) {
                $filterValue++;
            }
            $productCollection->addFieldToFilter($this->attributeCode, $filterValue);

            $this->addState($this->getStateLabel($filter), $filter);
            $this->isAdded = true;
        }

        return $this;
    }

    /**
     * Get filter text label
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getName()
    {
        $stockName = $this->additionalFiltersConfig->getStockFilterLabel($this->storeManager->getStore()->getStoreId());
        $stockName = ($stockName) ? : ExtraFiltersConfig::STOCK_FILTER_DEFAULT_LABEL;

        return $stockName;
    }

    /**
     * Get data array for building category filter items
     * @return array
     */
    protected function _getItemsData()
    {
        if (!$this->additionalFiltersConfig->isStockFilterEnabled($this->storeManager->getStore()->getStoreId())
            || $this->isAdded && !ConfigTrait::isMultiselectEnabled()) {
            return [];
        }

        $productCollection = $this->getLayer()->getProductCollection();
        $requestBuilder    = clone $productCollection->getCloneRequestBuilder();
        $requestBuilder->removePlaceholder($this->attributeCode);
        $queryRequest       = $requestBuilder->create();
        $optionsFacetedData = $this->filterDataService->getFilterBucketData($queryRequest, $this->attributeCode);

        $inStockValue    = 1; // for mysql
        $outOfStockValue = 0; // for mysql
        if ($this->elasticSearchService->isElasticEngineUsed()) {
            $inStockValue    = 2;
            $outOfStockValue = 1;
        }
        $optionsData = [
            [
                'label' => $this->getStateLabel(ExtraFiltersConfig::IN_STOCK_FILTER),
                'value' => ExtraFiltersConfig::IN_STOCK_FILTER,
                'count' => isset($optionsFacetedData[$inStockValue]) ? $optionsFacetedData[$inStockValue]['count'] : 0,
            ],
            [
                'label' => $this->getStateLabel(ExtraFiltersConfig::OUT_OF_STOCK_FILTER),
                'value' => ExtraFiltersConfig::OUT_OF_STOCK_FILTER,
                'count' => isset($optionsFacetedData[$outOfStockValue]) ? $optionsFacetedData[$outOfStockValue]['count'] : 0,
            ],
        ];
        foreach ($optionsData as $data) {
            if ($data['count'] < 1) {
                continue;
            }
            $this->itemDataBuilder->addItemData(
                $data['label'],
                $data['value'],
                $data['count']
            );
        }

        return $this->itemDataBuilder->build();
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @param string|array              $label
     * @param string|array<int, string> $filter
     * return void
     */
    private function addState($label, $filter)
    {
        $state = is_array($filter) ? $this->_requestVar . implode('_', $filter) : $this->_requestVar . $filter;
        if (isset(self::$isStateAdded[$state])) { //avoid double state adding (horizontal filters)
            return true;
        }

        if (is_array($filter) && !$label && $this->filterClearBlockConfig->isFilterClearBlockInOneRow()) {
            $labels = [];
            foreach ($filter as $filterValue) {
                $labels[] = $this->getStateLabel($filterValue);
            }
            $this->getLayer()->getState()
                ->addFilter($this->_createItem(implode(', ', $labels), $filter));
        } elseif (is_array($filter) && !$label) {
            foreach ($filter as $filterValue) {
                $this->getLayer()->getState()
                    ->addFilter($this->_createItem($this->getStateLabel($filterValue), $filterValue));
            }
        } else {
            $this->getLayer()->getState()->addFilter($this->_createItem($label, $filter));
        }

        self::$isStateAdded[$state] = true;

        return true;
    }

    /**
     * Get filter state label
     *
     * @param string $filter
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getStateLabel($filter)
    {
        $storeId    = $this->storeManager->getStore()->getStoreId();
        $stateLabel = ($filter == ExtraFiltersConfig::IN_STOCK_FILTER)
            ? $this->additionalFiltersConfig->getInStockFilterLabel($storeId)
            : $this->additionalFiltersConfig->getOutOfStockFilterLabel($storeId);

        if (!$stateLabel) {
            $stateLabel = ($filter == ExtraFiltersConfig::IN_STOCK_FILTER)
                ? 'In Stock'
                : 'Out of Stock';
        }

        return $stateLabel;
    }
}
