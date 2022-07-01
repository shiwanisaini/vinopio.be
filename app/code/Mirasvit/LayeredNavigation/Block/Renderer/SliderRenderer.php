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

use Magento\Framework\View\Element\Template;
use Mirasvit\LayeredNavigation\Api\Data\AttributeConfigInterface;
use Mirasvit\LayeredNavigation\Model\Config;
use Mirasvit\LayeredNavigation\Service\SliderService;

class SliderRenderer extends AbstractRenderer
{
    protected $_template = 'Mirasvit_LayeredNavigation::renderer/sliderRenderer.phtml';

    private   $config;

    private   $sliderService;

    public function __construct(
        Config $config,
        SliderService $sliderService,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->config        = $config;
        $this->sliderService = $sliderService;
    }

    public function isSlider()
    {
        return in_array($this->attributeConfig->getDisplayMode(), [
            AttributeConfigInterface::DISPLAY_MODE_SLIDER,
            AttributeConfigInterface::DISPLAY_MODE_SLIDER_FROM_TO,
        ]);
    }

    public function isFromTo()
    {
        return in_array($this->attributeConfig->getDisplayMode(), [
            AttributeConfigInterface::DISPLAY_MODE_FROM_TO,
            AttributeConfigInterface::DISPLAY_MODE_SLIDER_FROM_TO,
        ]);
    }

    public function getValueTemplate()
    {
        if ($this->getAttributeCode() === 'price') {
            $cs = $this->context->getStoreManager()->getStore()->getCurrentCurrency()
                ->getCurrencySymbol();

            return $cs . '{value.2}';
        }

        return $this->attributeConfig->getValueTemplate() ? $this->attributeConfig->getValueTemplate() : '{value}';
    }

    public function getSliderData()
    {
        return $this->filter->getSliderData($this->getSliderUrl());
    }

    /**
     * @return string
     */
    public function getSliderUrl()
    {
        return $this->sliderService->getSliderUrl($this->filter, $this->getSliderParamTemplate());
    }

    /**
     * @return string
     */
    public function getSliderParamTemplate()
    {
        return $this->sliderService->getParamTemplate($this->filter);
    }
}
