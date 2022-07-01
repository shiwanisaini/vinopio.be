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



namespace Mirasvit\LayeredNavigation\Block\Navigation;

use Magento\Catalog\Model\Layer\Filter\Item;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\View\Element\Template\Context;
use Magento\LayeredNavigation\Block\Navigation\State as NavigationState;
use Mirasvit\LayeredNavigation\Model\Config;
use Mirasvit\LayeredNavigation\Model\Config\HorizontalBarConfig;
use Mirasvit\LayeredNavigation\Model\Config\StateBarConfig;

/**
 * di.preference @see \Magento\LayeredNavigation\Block\Navigation\State
 */
class State extends NavigationState
{
    /**
     * @var string
     */
    protected $_template = 'navigation/state.phtml';

    private   $config;

    private   $storeId;

    private   $horizontalFiltersConfig;

    private   $filterClearBlockConfig;

    public function __construct(
        Config $config,
        HorizontalBarConfig $horizontalFiltersConfig,
        Context $context,
        LayerResolver $layerResolver,
        StateBarConfig $filterClearBlockConfig,
        array $data = []
    ) {
        $this->config                  = $config;
        $this->storeId                 = $context->getStoreManager()->getStore()->getStoreId();
        $this->horizontalFiltersConfig = $horizontalFiltersConfig;
        $this->filterClearBlockConfig  = $filterClearBlockConfig;

        parent::__construct($context, $layerResolver, $data);
    }

    /**
     * Retrieve active filters
     * @return Item[]
     */
    public function getActiveFilters()
    {
        $nameInLayout = $this->getNameInLayout();

        if ($this->filterClearBlockConfig->isHidden($this->storeId)) {
            return [];
        }

        if (($nameInLayout == HorizontalBarConfig::STATE_HORIZONTAL_BLOCK_NAME)
            && !$this->filterClearBlockConfig->isHorizontalPosition($this->storeId)) {
            return [];
        }

        if (($nameInLayout == HorizontalBarConfig::STATE_BLOCK_NAME
                || $nameInLayout == HorizontalBarConfig::STATE_SEARCH_BLOCK_NAME)
            && $this->filterClearBlockConfig->isHorizontalPosition($this->storeId)) {
            return [];
        }

        $filters = $this->getLayer()->getState()->getFilters();

        if (!is_array($filters)) {
            $filters = [];
        }

        return $filters;
    }

    public function isAjaxEnabled()
    {
        return $this->config->isAjaxEnabled();
    }

    /**
     * @return bool
     */
    public function isHorizontalFilter()
    {
        $nameInLayout = $this->getNameInLayout();
        if ($nameInLayout == HorizontalBarConfig::STATE_HORIZONTAL_BLOCK_NAME) {
            return true;
        }

        return false;
    }
}
