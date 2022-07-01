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
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Mirasvit\LayeredNavigation\Model\Config;
use Mirasvit\LayeredNavigation\Model\Config\Source\FilterApplyingModeSource;
use Mirasvit\LayeredNavigation\Service\FilterService;

class LabelRenderer extends AbstractRenderer
{
    protected $_template = 'Mirasvit_LayeredNavigation::renderer/labelRenderer.phtml';

    private   $filterService;

    private   $config;

    private   $highlightConfig;

    public function __construct(
        FilterService $filterService,
        Config $config,
        Config\HighlightConfig $highlightConfig,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->filterService   = $filterService;
        $this->config          = $config;
        $this->highlightConfig = $highlightConfig;
    }

    /**
     * @param Item $filterItem
     * @param bool $multiselect
     *
     * @return bool
     */
    public function isFilterItemChecked($filterItem, $multiselect = false)
    {
        return $this->filterService->isFilterItemChecked($filterItem, $multiselect);
    }

    public function isAjaxEnabled()
    {
        return $this->config->isAjaxEnabled();
    }

    public function isMultiselectEnabled()
    {
        return $this->config->isMultiselectEnabled();
    }

    public function getImageUrl(Item $filterItem)
    {
        foreach ($this->attributeConfig->getOptionsConfig() as $optionConfig) {
            if ($optionConfig->getOptionId() === $filterItem->getValueString()) {
                if ($optionConfig->getImagePath()) {
                    return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                        . 'tmp/catalog/product/' . $optionConfig->getImagePath();
                }
            }
        }

        return false;
    }

    public function isFullWidthImage(Item $filterItem)
    {
        foreach ($this->attributeConfig->getOptionsConfig() as $optionConfig) {
            if ($optionConfig->getOptionId() === $filterItem->getValueString()) {
                return $optionConfig->isFullImageWidth();
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isHighlightEnabled()
    {
        return $this->highlightConfig->isEnabled($this->storeId);
    }

    /**
     * @return string
     */
    public function getFilterItemDisplayMode()
    {
        return $this->config->getFilterItemDisplayMode();
    }

    public function isApplyingMode()
    {
        return $this->isAjaxEnabled() && $this->config->getApplyingMode() == FilterApplyingModeSource::OPTION_BY_BUTTON_CLICK;
    }
}
