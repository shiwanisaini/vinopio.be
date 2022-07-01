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



namespace Mirasvit\LayeredNavigation\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\LayeredNavigation\Model\Config;
use Mirasvit\LayeredNavigation\Model\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Model\Config\Source\FilterApplyingModeSource;

class ApplyButton extends Template
{
    use ConfigTrait;

    private $config;

    public function __construct(
        Config $config,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->config = $config;
    }

    public function isApplyingMode()
    {
        return $this->config->isAjaxEnabled()
            && $this->config->getApplyingMode() == FilterApplyingModeSource::OPTION_BY_BUTTON_CLICK;
    }
}
