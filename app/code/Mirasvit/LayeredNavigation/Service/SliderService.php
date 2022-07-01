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

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\LayeredNavigation\Model\Config;
use Mirasvit\SeoFilter\Model\Config as SeoFilterConfig;
use Mirasvit\SeoFilter\Service\RewriteService;
use Mirasvit\SeoFilter\Service\UrlService as SeoUrlService;

class SliderService
{
    const MATCH_PREFIX            = 'slider_match_prefix_';
    const SLIDER_DATA             = 'sliderdata';
    const SLIDER_URL_TEMPLATE     = self::SLIDER_REPLACE_VARIABLE . '_from-' . self::SLIDER_REPLACE_VARIABLE . '_to';
    const SLIDER_REPLACE_VARIABLE = '[attr]';

    /**
     * @var null|array
     */
    protected static $sliderOptions;

    private          $config;

    /**
     * @var RewriteService
     */
    private $rewrite;

    /**
     * @var RequestInterface
     */
    private $request;

    private $storeId;

    /**
     * @var SeoUrlService
     */
    private $urlHelper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        Config $config,
        SeoUrlService $urlHelper,
        RewriteService $rewrite
    ) {
        $this->request    = $request;
        $this->urlBuilder = $urlBuilder;
        $this->urlHelper  = $urlHelper;
        $this->rewrite    = $rewrite;
        $this->config     = $config;
        $this->storeId    = $storeManager->getStore()->getStoreId();
    }

    /**
     * @param array  $facetedData
     * @param string $requestVar
     * @param array  $fromToData
     * @param string $url
     *
     * @return array
     */
    public function getSliderData($facetedData, $requestVar, $fromToData, $url)
    {
        $sliderData = [
            'min'        => 0,
            'max'        => 0,
            'requestVar' => 0,
            'from'       => 0,
            'to'         => 0,
            'url'        => 0,
        ];

        $sliderDataKey = $this->getSliderDataKey($requestVar);

        if (!isset($facetedData[$sliderDataKey])) {
            return $sliderData;
        }

        $min  = floatval($facetedData[$sliderDataKey]['min']);
        $max  = floatval($facetedData[$sliderDataKey]['max']);
        $from = ($fromToData) ? $fromToData['from'] : $min;
        $to   = ($fromToData) ? $fromToData['to'] : $max;

        $sliderData = [
            'min'        => $min,
            'max'        => $max,
            'requestVar' => $requestVar,
            'from'       => $from,
            'to'         => $to,
            'url'        => $url,
        ];


        return $sliderData;
    }

    /**
     * @param FilterInterface $filter
     * @param string          $template
     *
     * @return string
     */
    public function getSliderUrl(FilterInterface $filter, $template)
    {
        if ($this->config->isSeoFiltersEnabled()
            && in_array($this->request->getFullActionName(), [
                'catalog_category_view',
                'all_products_page_index_index',
                'brand_brand_view',
            ])
        ) {
            return $this->getSliderSeoFriendlyUrl($filter, $template);
        }

        $query = [$filter->getRequestVar() => $template];

        return $this->urlBuilder->getUrl('*/*/*', [
            '_current'     => true,
            '_use_rewrite' => true,
            '_query'       => $query,
        ]);
    }

    /**
     * @param FilterInterface $filter
     *
     * @return string
     */
    public function getParamTemplate(FilterInterface $filter)
    {
        $requestVar = $filter->getRequestVar();

        return str_replace(
            SliderService::SLIDER_REPLACE_VARIABLE,
            $requestVar,
            SliderService::SLIDER_URL_TEMPLATE
        );
    }

    /**
     * @param string $attributeCode
     *
     * @return string
     */
    public function getRegisterMatchedValue($attributeCode)
    {
        return SliderService::MATCH_PREFIX . $this->getSliderDataKey($attributeCode);
    }

    /**
     * @param string $attributeCode
     *
     * @return string
     */
    public function getSliderDataKey($attributeCode)
    {
        return SliderService::SLIDER_DATA . str_replace('_', '', $attributeCode);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @param FilterInterface $filter
     * @param string          $template
     *
     * @return string|string[]
     */
    protected function getSliderSeoFriendlyUrl(FilterInterface $filter, $template)
    {
        $activeFilters = $this->rewrite->getActiveFilters();
        if (!$activeFilters || $this->isFilterCategoryOnly($activeFilters)) {
            $separator = '/';
        } else {
            $separator = SeoFilterConfig::SEPARATOR_FILTERS;
        }
        $price      = $filter->getRequestVar() . SeoFilterConfig::SEPARATOR_DECIMAL . $template;
        $currentUrl = $this->urlBuilder->getCurrentUrl();
        $suffix     = $this->urlHelper->getCategoryUrlSuffix($this->storeId);

        if (isset($activeFilters[$filter->getRequestVar()])) { //delete old param from url
            $currentUrlPrepared      = strtok($currentUrl, '?');
            $currentUrlPreparedArray = explode('/', $currentUrlPrepared);
            $priceValue              = $currentUrlPreparedArray[count($currentUrlPreparedArray) - 1];
            $priceValue              = ($suffix) ? str_replace($suffix, '', $priceValue) : $priceValue;
            $priceValueArray         = explode($filter->getRequestVar(), $priceValue);
            if (isset($priceValueArray[1])) {
                $priceValue = $filter->getRequestVar() . $priceValueArray[1];
            }
            $currentUrl = str_replace($priceValue, '', $currentUrl);
        }

        if (($suffix && $suffix !== '/') && strpos($currentUrl, $suffix) !== false) {
            $currentUrl = str_replace($suffix, $separator . $price . $suffix, $currentUrl);
        } elseif (strpos($currentUrl, '?') !== false) {
            $currentUrl = str_replace('?', $separator . $price . '?', $currentUrl);
        } else {
            $currentUrl = rtrim($currentUrl, $separator) . $separator . $price;
        }

        $currentUrl = str_replace(
            SeoFilterConfig::SEPARATOR_FILTERS . SeoFilterConfig::SEPARATOR_FILTERS,
            SeoFilterConfig::SEPARATOR_FILTERS,
            $currentUrl
        );
        $currentUrl = str_replace('/' . SeoFilterConfig::SEPARATOR_FILTERS, '/', $currentUrl);

        return $currentUrl;
    }

    /**
     * @param array|null $activeFilters
     *
     * @return bool
     */
    private function isFilterCategoryOnly($activeFilters)
    {
        if (!is_array($activeFilters)) {
            return false;
        }
        if (count($activeFilters) == 1 && array_key_exists('cat', $activeFilters)) {
            return true;
        }

        return false;
    }
}
