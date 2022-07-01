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



namespace Mirasvit\QuickNavigation\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider
{
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled()
    {
        return (bool)$this->scopeConfig->getValue('mst_quick_navigation/general/is_enabled');
    }

    public function getTotalThreshold()
    {
        return (int)$this->scopeConfig->getValue('mst_quick_navigation/general/total_threshold');
    }

    public function getAttributeThreshold()
    {
        return (int)$this->scopeConfig->getValue('mst_quick_navigation/general/attribute_threshold');
    }
}
