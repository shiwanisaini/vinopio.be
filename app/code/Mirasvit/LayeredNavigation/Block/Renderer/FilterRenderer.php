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

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\LayeredNavigation\Block\Navigation\FilterRenderer as GenericFilterRenderer;
use Mirasvit\LayeredNavigation\Api\Data\AttributeConfigInterface;
use Mirasvit\LayeredNavigation\Model\Config\ExtraFiltersConfig;
use Mirasvit\LayeredNavigation\Repository\AttributeConfigRepository;

/**
 * Preference (di.xml) for @see \Magento\LayeredNavigation\Block\Navigation\FilterRenderer
 */
class FilterRenderer extends GenericFilterRenderer
{
    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * @var AttributeConfigInterface
     */
    private $attributeConfig;

    private $attributeConfigRepository;

    public function __construct(
        AttributeConfigRepository $attributeConfigRepository,
        Context $context,
        array $data = []
    ) {
        $this->attributeConfigRepository = $attributeConfigRepository;

        parent::__construct($context, $data);
    }

    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param FilterInterface $filter
     *
     * @return string
     */
    public function render(FilterInterface $filter)
    {
        $this->filter = $filter;

        $this->attributeConfig = $this->attributeConfigRepository->getByAttributeCode($filter->getRequestVar());
        $this->attributeConfig = $this->attributeConfig ? : $this->attributeConfigRepository->create();

        $this->setTemplate('Mirasvit_LayeredNavigation::renderer/filter.phtml');

        return parent::render($filter);
    }

    /**
     * @return AbstractRenderer
     */
    public function getRendererBlock()
    {
        if (in_array($this->attributeConfig->getDisplayMode(), [
            AttributeConfigInterface::DISPLAY_MODE_SLIDER,
            AttributeConfigInterface::DISPLAY_MODE_FROM_TO,
            AttributeConfigInterface::DISPLAY_MODE_SLIDER_FROM_TO,
        ])) {
            /** @var AbstractRenderer $rendererBlock */
            $rendererBlock = $this->_layout->createBlock(SliderRenderer::class);
        } else {
            $rendererBlock = $this->_layout->createBlock(LabelRenderer::class);
        }

        if ($this->filter->getRequestVar() === ExtraFiltersConfig::RATING_FILTER_FRONT_PARAM) {
            $rendererBlock = $this->_layout->createBlock(RatingRenderer::class);
        }

        if ($this->filter->getRequestVar() === 'cat') {
            $rendererBlock = $this->_layout->createBlock(CategoryRenderer::class);
        }

        $rendererBlock->setFilterData($this->filter, $this->attributeConfig);

        return $rendererBlock;
    }
}
