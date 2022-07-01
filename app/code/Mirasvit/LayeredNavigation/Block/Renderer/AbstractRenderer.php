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
use Magento\Catalog\Model\Layer\Filter\Item;
use Magento\Framework\View\Element\Template;
use Mirasvit\LayeredNavigation\Api\Data\AttributeConfigInterface;

abstract class AbstractRenderer extends Template
{
    /**
     * @var FilterInterface
     */
    protected $filter;

    /**
     * @var AttributeConfigInterface
     */
    protected $attributeConfig;

    protected $context;

    protected $storeId;

    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        $this->context = $context;
        $this->storeId = $context->getStoreManager()->getStore()->getId();

        parent::__construct($context, $data);
    }

    public function setFilterData(FilterInterface $filter, AttributeConfigInterface $attributeConfig)
    {
        $this->filter          = $filter;
        $this->attributeConfig = $attributeConfig;

        return $this;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return Item[]
     */
    public function getFilterItems()
    {
        return $this->filter->getItems();
    }

    public function getAttributeCode()
    {
        return $this->filter->getRequestVar();
    }

    public function getItemId(Item $filterItem)
    {
        return 'm_' . $this->getFilter()->getRequestVar() . '[' . $filterItem->getValueString() . ']';
    }

    public function getCountElement(Item $filterItem)
    {
        /** @var Template $block */
        $block = $this->_layout->createBlock(Template::class);
        $block->setTemplate('Mirasvit_LayeredNavigation::renderer/element/count.phtml')
            ->setData('count', $filterItem->getData('count'));

        return $block->toHtml();
    }

    public function getSizeLimiterElement()
    {
        /** @var Element\SizeLimiterElement $block */
        $block = $this->_layout->createBlock(Element\SizeLimiterElement::class);
        $block->setFilter($this->filter)
            ->setTemplate('Mirasvit_LayeredNavigation::renderer/element/sizeLimiter.phtml');

        return $block->toHtml();
    }

    public function getSearchBoxElement()
    {
        /** @var Element\SearchBoxElement $block */
        $block = $this->_layout->createBlock(Element\SearchBoxElement::class);
        $block->setFilter($this->filter)
            ->setAttributeConfig($this->attributeConfig)
            ->setTemplate('Mirasvit_LayeredNavigation::renderer/element/searchBox.phtml');

        return $block->toHtml();
    }
}
