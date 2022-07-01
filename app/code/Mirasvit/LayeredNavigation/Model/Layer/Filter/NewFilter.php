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
use Mirasvit\LayeredNavigation\Service\Filter\FilterNewService;
use Mirasvit\LayeredNavigation\Service\FilterDataService;

/**
 * New filter
 */
class NewFilter extends AbstractFilter
{
    use ConfigTrait;

    /**
     * @var array
     */
    protected static $isStateAdded = [];

    /**
     * @var string
     */
    protected $attributeCode = ExtraFiltersConfig::NEW_FILTER;

    /**
     * @var bool
     */
    protected $isAdded = false;

    private          $filterDataService;

    private          $filterNewService;

    private          $additionalFiltersConfig;

    private          $storeManager;

    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        DataBuilder $itemDataBuilder,
        FilterDataService $filterDataService,
        FilterNewService $filterNewService,
        ExtraFiltersConfig $additionalFiltersConfig,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->_requestVar             = ExtraFiltersConfig::NEW_FILTER_FRONT_PARAM;
        $this->filterDataService       = $filterDataService;
        $this->filterNewService        = $filterNewService;
        $this->additionalFiltersConfig = $additionalFiltersConfig;
        $this->storeManager            = $storeManager;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->additionalFiltersConfig->isNewFilterEnabled($this->storeManager->getStore()->getStoreId())) {
            return $this;
        }
        $filter = $request->getParam(ExtraFiltersConfig::NEW_FILTER_FRONT_PARAM);

        if ($filter) {
            $this->filterNewService->addNewFilterInCollection($this->getLayer()->getProductCollection());
            $this->addState(__('Yes'), $filter);
            $this->isAdded = true;
        }

        return $this;
    }

    /**
     * @param string $label
     * @param string $filter
     * return void
     */
    private function addState($label, $filter)
    {
        $state = $this->_requestVar . $filter;
        if (isset(self::$isStateAdded[$state])) { //avoid double state adding (horizontal filters)
            return true;
        }
        $this->getLayer()->getState()->addFilter($this->_createItem($label, $filter));

        self::$isStateAdded[$state] = true;

        return true;
    }

    /**
     * Get data array for building category filter items
     * @return array
     */
    protected function _getItemsData()
    {
        if (!$this->additionalFiltersConfig->isNewFilterEnabled($this->storeManager->getStore()->getStoreId())
            || $this->isAdded && !ConfigTrait::isMultiselectEnabled()) {
            return [];
        }

        $productCollection = $this->getLayer()->getProductCollection();
        $requestBuilder    = clone $productCollection->getCloneRequestBuilder();
        $requestBuilder->removePlaceholder($this->attributeCode);
        $queryRequest       = $requestBuilder->create();
        $optionsFacetedData = $this->filterDataService->getFilterBucketData($queryRequest, $this->attributeCode);

        $count              = isset($optionsFacetedData[1]) ? $optionsFacetedData[1]['count'] : 0;

        $optionsData = [
            [
                'label' => $this->getName(),
                'value' => 1,
                'count' => $count,
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
     * Get filter text label
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getName()
    {
        $newName = $this->additionalFiltersConfig->getNewFilterLabel($this->storeManager->getStore()->getStoreId());
        $newName = ($newName) ? : ExtraFiltersConfig::NEW_FILTER_DEFAULT_LABEL;

        return $newName;
    }
}
