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



namespace Mirasvit\LayeredNavigation\Block\Renderer\Element;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\View\Element\Template;
use Mirasvit\LayeredNavigation\Api\Data\AttributeConfigInterface;
use Mirasvit\LayeredNavigation\Model\Config\SizeLimiterConfig;

class SearchBoxElement extends Template
{
    /**
     * @var FilterInterface
     */
    private $filter;

    /**
     * @var AttributeConfigInterface
     */
    private $attributeConfig;

    private $sizeLimiterConfig;

    public function __construct(
        SizeLimiterConfig $sizeLimiterConfig,
        Template\Context $context,
        array $data = []
    ) {
        $this->sizeLimiterConfig = $sizeLimiterConfig;

        parent::__construct($context, $data);
    }

    public function setFilter(FilterInterface $filter)
    {
        $this->filter = $filter;

        return $this;
    }

    public function setAttributeConfig(AttributeConfigInterface $attributeConfig)
    {
        $this->attributeConfig = $attributeConfig;

        return $this;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function isShowSearchBox()
    {
        return $this->attributeConfig->isShowSearchBox();
    }
}
