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



namespace Mirasvit\LayeredNavigation\Service;

use Mirasvit\LayeredNavigation\Model\Config;
use Mirasvit\LayeredNavigation\Model\Config\ExtraFiltersConfig;
use Mirasvit\LayeredNavigation\Model\Config\HighlightConfig;
use Mirasvit\LayeredNavigation\Model\Config\HorizontalBarConfig;
use Mirasvit\LayeredNavigation\Model\Config\StateBarConfig;

class CssCreatorService
{
    private $additionalFiltersConfig;

    private $horizontalFiltersConfig;

    private $highlightConfig;

    private $config;

    private $filterClearBlockConfig;

    public function __construct(
        ExtraFiltersConfig $additionalFiltersConfig,
        HorizontalBarConfig $horizontalFiltersConfig,
        HighlightConfig $highlightConfig,
        Config $config,
        StateBarConfig $filterClearBlockConfig
    ) {
        $this->additionalFiltersConfig = $additionalFiltersConfig;
        $this->horizontalFiltersConfig = $horizontalFiltersConfig;
        $this->highlightConfig         = $highlightConfig;
        $this->config                  = $config;
        $this->filterClearBlockConfig  = $filterClearBlockConfig;
    }

    /**
     * @param int $storeId
     *
     * @return string
     */
    public function getCssContent($storeId)
    {
        $css = '';
        $css = $this->getHorizontalFiltersCss($storeId, $css);
        $css = $this->getHighlightColorCss($storeId, $css);
        $css = $this->getFilterClearBlockCss($storeId, $css);

        $css = $this->getDisplayOptionsCss($storeId, $css);
        $css = $this->getShowOpenedFiltersCss($storeId, $css);

        return $css;
    }


    /**
     * @param int    $storeId
     * @param string $css
     *
     * @return string
     */
    private function getHorizontalFiltersCss($storeId, $css)
    {
        if ($hideHorizontalFiltersValue = $this->horizontalFiltersConfig->getHideHorizontalFiltersValue($storeId)) {
            $hideHorizontalFiltersValue = str_replace('px', '', $hideHorizontalFiltersValue); //delete px if exist
            $css                        .= '/* Hide horizontal filters if screen size is less than (px) - begin */';
            $css                        .= '@media all and (max-width: ' . $hideHorizontalFiltersValue . 'px) {';
            $css                        .= '.navigation-horizontal .block-subtitle.filter-subtitle {display: none !important;} ';
            $css                        .= '.navigation-horizontal .filter-options {display: none !important;} ';
            $css                        .= '} ';
            $css                        .= '/* Hide horizontal filters if screen size is less than (px) - end */';
        }

        if ($this->horizontalFiltersConfig->getFilters($storeId)) {
            $css .= '/* Show horizontal clear filter panel - begin */';
            $css .= '.navigation-horizontal {display: block;} ';
            $css .= '.navigation-horizontal .block-subtitle.filter-subtitle {display: block} ';
            $css .= '.navigation-horizontal .filter-options {display: block} ';
            $css .= '/* Show horizontal clear filter panel - end */ ';
        }

        //show only horizontal filters
        if ($this->horizontalFiltersConfig->getFilters($storeId)
            == Config\Source\HorizontalFilterOptions::ALL_FILTERED_ATTRIBUTES) {
            $css .= '/* Show only horizontal filters - begin */';
            $css .= '.navigation-horizontal .filter-options {display:block !important;} ';
            $css .= '.sidebar.sidebar-main .block-title {display:none!important;} ';
            $css .= '.sidebar.sidebar-additional {display:none!important;} ';
            $css .= '.columns .column.main {width: 100%;} ';
            $css
                 .= 'form[m-navigation-filter="RatingFilter"] .filter-options-content a {margin: 0 !important;
                padding: 0 !important;} ';
            $css .= '/* Show only horizontal filters - end */ ';
        }

        return $css;
    }

    /**
     * @param int    $storeId
     * @param string $css
     *
     * @return string
     */
    private function getFilterClearBlockCss($storeId, $css)
    {
        if ($this->filterClearBlockConfig->isHorizontalPosition($storeId)) {
            $css .= '/* Show horizontal clear filter panel - begin */';
            $css .= '.navigation-horizontal {display: block !important;} ';
            $css .= '@media all and (mix-width: 767px) {';
            $css .= '.navigation-horizontal .block-actions.filter-actions {display: block !important;} ';
            $css .= '} ';
            $css .= '@media all and (max-width: 767px) {';
            $css .= '.navigation-horizontal .block-title.filter-title {display: none !important;} ';
            $css .= '} ';
            $css .= '.sidebar .block-actions.filter-actions {display: none;} ';
            $css .= '/* Show horizontal clear filter panel - end */';
        } else {
            $css .= '.navigation-horizontal .block-actions.filter-actions {display: none;} ';
        }

        if ($this->filterClearBlockConfig->isHidden($storeId)) {
            $css .= '.sidebar .block-actions.filter-actions {display: none;} ';
        }

        return $css;
    }

    /**
     * @param int    $storeId
     * @param string $css
     *
     * @return string
     */
    private function getHighlightColorCss($storeId, $css)
    {
        $color = $this->highlightConfig->getColor($storeId);

        $css .= $this->getStyle('.mst-nav__label .mst-nav__label-item._highlight a', [
            'color' => $color,
        ]);

        //        $css .= '.item .m-navigation-link-highlight { color:' . $color . '; } ';
        //        $css .= '.m-navigation-highlight-swatch .swatch-option.selected { outline: 2px solid ' . $color . '; } ';
        //        $css .= '.m-navigation-filter-item .swatch-option.image:not(.disabled):hover { outline: 2px solid'
        //            . $color . '; border: 1px solid #fff; } ';
        //        $css .= '.swatch-option.image.m-navigation-highlight-swatch { outline: 2px solid'
        //            . $color . '; 1px solid #fff; } ';
        //        $css .= '.m-navigation-swatch .swatch-option:not(.disabled):hover { outline: 2px solid'
        //            . $color . '; border: 1px solid #fff;  color: #333; } ';
        //        $css .= '.m-navigation-swatch .m-navigation-highlight-swatch .swatch-option { outline: 2px solid'
        //            . $color . '; border: 1px solid #fff;  color: #333; } ';
        //

        return $css;
    }

    /**
     * @param int    $storeId
     * @param string $css
     *
     * @return string
     */
    private function getDisplayOptionsCss($storeId, $css)
    {
        if ($backgroundColor = $this->config->getDisplayOptionsBackgroundColor()) {
            $css
                .= '.checkbox input[type="checkbox"]:checked + label::before,
                      .checkbox input[type="radio"]:checked + label::before { background-color:'
                . $backgroundColor . '; } ';
        }
        if ($borderColor = $this->config->getDisplayOptionsBorderColor()) {
            $css
                .= '.checkbox input[type="checkbox"]:checked + label::before,
                      .checkbox input[type="radio"]:checked + label::before { border-color:'
                . $borderColor . '; } ';
        }
        if ($checkedLabelColor = $this->config->getDisplayOptionsCheckedLabelColor()) {
            $css
                .= '.checkbox input[type="checkbox"]:checked+label::after,
                     .checkbox input[type="radio"]:checked+label::after { color:'
                . $checkedLabelColor . '; } ';
        }

        return $css;
    }

    /**
     * @param int    $storeId
     * @param string $css
     *
     * @return string
     */
    private function getShowOpenedFiltersCss($storeId, $css)
    {
        if ($isShowOpenedFilters = $this->config->isOpenFilter()) {
            $css .= '.sidebar .filter-options .filter-options-content { display: block; } ';
        }

        return $css;
    }

    /**
     * @param string $selector
     * @param array  $styles
     *
     * @return string
     */
    private function getStyle($selector, array $styles)
    {
        $arr = [];

        foreach ($styles as $key => $value) {
            if ($value) {
                $arr[] = $key . ': ' . $value . ';';
            }
        }

        return $selector . '{' . implode($arr) . '}';
    }
}
