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
use Mirasvit\LayeredNavigation\Model\Config\HorizontalBarConfig;
use Mirasvit\LayeredNavigation\Model\Config\StateBarConfig;
use Mirasvit\LayeredNavigation\Service\FilterService;
use Mirasvit\LayeredNavigation\Model\Config\Source\FilterApplyingModeSource;
use Magento\Framework\App\ObjectManager;

class Ajax extends Template
{
    use ConfigTrait;

    private $filterService;

    private $config;

    private $filterClearBlockConfig;

    private $horizontalFiltersConfig;

    private $storeId;

    private $highlightConfig;

    public function __construct(
        Context $context,
        FilterService $filterService,
        Config $config,
        Config\HighlightConfig $highlightConfig,
        StateBarConfig $filterClearBlockConfig,
        HorizontalBarConfig $horizontalFiltersConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->filterService           = $filterService;
        $this->config                  = $config;
        $this->filterClearBlockConfig  = $filterClearBlockConfig;
        $this->horizontalFiltersConfig = $horizontalFiltersConfig;
        $this->storeId                 = $context->getStoreManager()->getStore()->getStoreId();
        $this->highlightConfig         = $highlightConfig;
    }

    /**
     * @return array
     */
    public function getJsonConfig()
    {
        return [
            '*' => [
                'Mirasvit_LayeredNavigation/js/ajax' => [
                    'cleanUrl'                   => $this->getCleanUrl(),
                    'overlayUrl'                 => $this->getOverlayUrl(),
                    'isSeoFilterEnabled'         => $this->isSeoFilterEnabled(),
                    'isFilterClearBlockInOneRow' => $this->isFilterClearBlockInOneRow(),
                    'isHorizontalByDefault'      => $this->isUseCatalogLeftnavHorisontalNavigation(),
                ],
            ],
        ];
    }

    public function isIntantMode()
    {
        return $this->config->isAjaxEnabled()
            && $this->config->getApplyingMode() == FilterApplyingModeSource::OPTION_INSTANTLY;
    }

    public function isConfirmationMode()
    {
        return $this->config->isAjaxEnabled()
            && $this->config->getApplyingMode() == FilterApplyingModeSource::OPTION_BY_BUTTON_CLICK;
    }

    /**
     * @return string
     */
    private function getCleanUrl()
    {
        $activeFilters = [];

        foreach ($this->filterService->getActiveFilters() as $item) {
            $filter = $item->getFilter();

            $activeFilters[$filter->getRequestVar()] = $filter->getCleanValue();
        }

        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $activeFilters;
        $params['_escape']      = true;

        $url = $this->_urlBuilder->getUrl('*/*/*', $params);
        $url = str_replace('&amp;', '&', $url);

        return $url;
    }

    public function getFriendlyClearUrl()
    {
        return ObjectManager::getInstance()->get('\Mirasvit\SeoFilter\Service\FriendlyUrlService')->getClearUrl();
    }

    /**
     * @return string
     */
    private function getOverlayUrl()
    {
        return $this->getViewFileUrl('Mirasvit_LayeredNavigation::images/ajax_loading.gif');
    }

    /**
     * @return string
     */
    public function isSeoFilterEnabled()
    {
        return $this->config->isSeoFiltersEnabled();
    }

    /**
     * @return int
     */
    private function isFilterClearBlockInOneRow()
    {
        return $this->filterClearBlockConfig->isFilterClearBlockInOneRow();
    }

    /**
     * @return int
     */
    private function isUseCatalogLeftnavHorisontalNavigation()
    {
        return $this->horizontalFiltersConfig->isUseCatalogLeftnavHorisontalNavigation($this->storeId);
    }

    public function isHighlightEnabled()
    {
        return $this->highlightConfig->isEnabled($this->storeId);
    }
}
