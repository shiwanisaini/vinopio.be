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

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\View\Element\Template\Context;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered;
use Magento\Swatches\Helper\Data as SwatchesHelperData;
use Magento\Swatches\Helper\Media as SwatchesHelperMedia;
use Mirasvit\LayeredNavigation\Api\Data\AttributeConfigInterface;
use Mirasvit\LayeredNavigation\Model\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Repository\AttributeConfigRepository;
use Mirasvit\LayeredNavigation\Service\FilterService;
use Mirasvit\LayeredNavigation\Model\Config\Source\FilterApplyingModeSource;

/**
 * Preference (di.xml) for @see \Magento\Swatches\Block\LayeredNavigation\RenderLayered
 */
class SwatchRenderer extends RenderLayered
{
    use ConfigTrait;

    private $attributeConfigRepository;

    private $filterService;

    /**
     * @var AttributeConfigInterface
     */
    private $attributeConfig;

    public function __construct(
        FilterService $filterService,
        AttributeConfigRepository $attributeConfigRepository,
        Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        SwatchesHelperData $swatchHelper,
        SwatchesHelperMedia $mediaHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $eavAttribute,
            $layerAttribute,
            $swatchHelper,
            $mediaHelper,
            $data
        );

        $this->filterService             = $filterService;
        $this->attributeConfigRepository = $attributeConfigRepository;
    }

    public function setSwatchFilter(AbstractFilter $filter)
    {
        $this->attributeConfig = $this->attributeConfigRepository->getByAttributeCode($filter->getRequestVar());

        return parent::setSwatchFilter($filter);
    }

    public function getDisplayMode()
    {
        return $this->attributeConfig->getDisplayMode();
    }

    /**
     * Get relevant path to template
     * @return string
     */
    public function getTemplate()
    {
        return 'Mirasvit_LayeredNavigation::renderer/swatchRenderer.phtml';
    }

    /**
     * @return AbstractFilter
     */
    public function getSwatchFilter()
    {
        return $this->filter;
    }

    /**
     * @param AbstractFilter $filter
     *
     * @return string
     */
    public function getFilterUniqueValue($filter)
    {
        return $this->filterService->getFilterUniqueValue($filter);
    }

    /**
     * @return string
     */
    public function getFilterRequestVar()
    {
        $filter = $this->getSwatchFilter();
        if (!is_object($filter)) {
            return '';
        }

        return $filter->getRequestVar();
    }

    /**
     * @param string $option
     *
     * @return bool
     */
    public function isItemChecked($option)
    {
        return $this->filterService->isFilterCheckedSwatch($this->filter->getRequestVar(), $option);
    }

    /**
     * @return array
     */
    public function getSwatchData()
    {
        $swatchData      = parent::getSwatchData();
        $attributeConfig = $this->attributeConfigRepository->getByAttributeCode($swatchData['attribute_code']);

        if ($attributeConfig) {
            $attributeConfig = $attributeConfig->getConfig();
            $swatchData      = array_merge($attributeConfig, $swatchData);
        }

        return $swatchData;
    }

    /**
     * @param string $attributeCode
     * @param int    $optionId
     *
     * @return string
     */
    public function getRemoveUrl($attributeCode, $optionId)
    {
        return $this->buildUrl($attributeCode, $optionId);
    }

    /**
     * @param string $attributeCode
     * @param string $optionId
     *
     * @return string|string[]
     */
    public function getSwatchOptionLink($attributeCode, $optionId)
    {
        return $this->buildUrl($attributeCode, $optionId);
    }

    public function isApplyingMode()
    {
        return $this->isAjaxEnabled() && $this->getApplyingMode() == FilterApplyingModeSource::OPTION_BY_BUTTON_CLICK;
    }
}
