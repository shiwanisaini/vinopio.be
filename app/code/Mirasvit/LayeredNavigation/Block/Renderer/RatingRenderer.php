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
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Mirasvit\LayeredNavigation\Model\Config;
use Mirasvit\LayeredNavigation\Service\FilterService;

class RatingRenderer extends AbstractRenderer
{
    protected $_template = 'Mirasvit_LayeredNavigation::renderer/ratingRenderer.phtml';

    private $filterService;

    private $registry;

    public function __construct(
        FilterService $filterService,
        Registry $registry,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->filterService = $filterService;
        $this->registry      = $registry;
    }

    public function getItemRating(Item $filterItem)
    {
        $data = $this->registry->registry(Config\ExtraFiltersConfig::RATING_FILTER_DATA);

        return $data[$filterItem->getValueString()]['value'];
    }

    /**
     * @param Item $filterItem
     * @param bool $multiselect
     *
     * @return bool
     */
    public function isFilterChecked(Item $filterItem, $multiselect = false)
    {
        return $this->filterService->isFilterItemChecked($filterItem, $multiselect);
    }
}
