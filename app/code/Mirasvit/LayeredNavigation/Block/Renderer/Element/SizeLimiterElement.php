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
use Mirasvit\LayeredNavigation\Model\Config\SizeLimiterConfig;
use Mirasvit\LayeredNavigation\Model\Config\Source\SizeLimiterDisplayModeSource;

class SizeLimiterElement extends Template
{
    private $sizeLimiterConfig;

    /**
     * @var FilterInterface
     */
    private $filter;

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

    public function getAttributeCode()
    {
        return $this->filter->getRequestVar();
    }

    public function isScrollMode()
    {
        return $this->sizeLimiterConfig->getDisplayMode() == SizeLimiterDisplayModeSource::MODE_SCROLL
            && $this->getScrollHeight();
    }

    public function isShowHideMode()
    {
        return $this->sizeLimiterConfig->getDisplayMode() == SizeLimiterDisplayModeSource::MODE_SHOW_HIDE
            && $this->getLinkLimit()
            && $this->filter->getItemsCount() > $this->getLinkLimit();
    }

    public function getScrollHeight()
    {
        return (int)$this->sizeLimiterConfig->getScrollHeight();
    }

    public function getLinkLimit()
    {
        return (int)$this->sizeLimiterConfig->getLinkLimit();
    }

    public function getTextLess()
    {
        return $this->sizeLimiterConfig->getTextLess();
    }

    public function getTextMore()
    {
        return $this->sizeLimiterConfig->getTextMore();
    }
}
