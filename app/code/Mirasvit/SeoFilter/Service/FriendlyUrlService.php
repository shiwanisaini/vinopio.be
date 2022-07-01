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
 * @package   mirasvit/module-seo-filter
 * @version   1.0.29
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Service;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Mirasvit\SeoFilter\Model\Config;
use Mirasvit\SeoFilter\Model\Context;
use function GuzzleHttp\Psr7\build_query;

class FriendlyUrlService
{
    const QUERY_FILTERS = ['cat'];

    private $rewriteService;

    private $urlService;

    private $context;

    private $config;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    public function __construct(
        RequestInterface $request,
        RewriteService $rewrite,
        UrlService $urlService,
        Config $config,
        Context $context
    ) {
        $this->request        = $request;
        $this->rewriteService = $rewrite;
        $this->urlService     = $urlService;
        $this->context        = $context;
        $this->config         = $config;
    }

    /**
     * Create friendly urls for Layered Navigation (add and remove filters)
     *
     * @param string $attributeCode
     * @param string $filterValue ex: 100  50-60,60-   234,203
     * @param bool   $remove
     *
     * @return string|false
     */
    public function getUrl($attributeCode, $filterValue, $remove = false)
    {
        if (is_array($filterValue)) {
            $values = $filterValue;
        } else {
            $values = explode(Config::SEPARATOR_FILTER_VALUES, $filterValue);
        }

        $requiredFilters[$attributeCode] = [];
        if ($attributeCode != '') {
            foreach ($values as $value) {
                $requiredFilters[$attributeCode][$value] = $value;
            }
        }

        // merge with previous filters
        foreach ($this->rewriteService->getActiveFilters() as $attr => $filters) {
            if (!$this->config->isMultiselectEnabled() && $attr == $attributeCode) {
                continue;
            }

            foreach ($filters as $filter) {
                if ($filter == $filterValue) {
                    unset($requiredFilters[$attr][$filter]);
                    continue;
                }

                $requiredFilters[$attr][$filter] = $filter;
            }
        }

        // remove filter
        if ($attributeCode != '') {
            if ($remove && isset($requiredFilters[$attributeCode])) {
                foreach ($values as $value) {
                    unset($requiredFilters[$attributeCode][$value]);
                }
            }
        }

        // merge all filters on one line f1-f2-f3-f4
        $filterLines = [];
        $queryParams = [];
        foreach ($requiredFilters as $attrCode => $filters) {
            $filterLine = [];
            $queryParam = [];

            foreach ($filters as $filter) {
                if (in_array($attrCode, self::QUERY_FILTERS)) {
                    $queryParam[] = $filter;
                } else {
                    $filterLine[] = $this->rewriteService->getRewrite($attrCode, $filter);
                }
            }

            if (in_array($attrCode, self::QUERY_FILTERS) && count($filters) == 0) {
                $queryParam[] = '';
            }

            if (count($queryParam)) {
                $queryParams[$attrCode] = implode(',', $queryParam);
            }

            if (count($filterLine)) {
                $filterLines[] = implode(Config::SEPARATOR_FILTERS, $filterLine);
            }
        }

        $filterLines = implode(Config::SEPARATOR_FILTERS, $filterLines);
        //sort filters
        $values = explode(Config::SEPARATOR_FILTERS, $filterLines);
        asort($values);
        $filterString = implode(Config::SEPARATOR_FILTERS, $values);

        //add extra query params
        foreach ($this->urlService->getGetParams() as $param => $value) {
            if (!array_key_exists($param, $requiredFilters)) {
                $queryParams[$param] = $value;
            }
        }

        $url = $this->getPreparedCurrentUrl($filterString, $queryParams);

        return $url;
    }

    /**
     * @param string $filterUrlString
     * @param array  $queryParams
     *
     * @return string
     */
    public function getPreparedCurrentUrl($filterUrlString, $queryParams)
    {
        $suffix = $this->getSuffix();
        $url    = $this->getClearUrl();
        //        var_dump($suffix);
        $url = preg_replace('/\?.*/', '', $url);
        $url = ($suffix && $suffix !== '/') ? str_replace($suffix, '', $url) : $url;
        if (!empty($filterUrlString)) {
            if ($separator = $this->config->getCustomSeparator()) {
                $url .= (substr($url, -1, 1) === '/' ? '' : '/') . $separator;
            }

            $url .= (substr($url, -1, 1) === '/' ? '' : '/') . $filterUrlString;
        }

        $url   = $url . $suffix;
        $query = '';
        if (count($queryParams)) {
            $query = '?' . build_query($queryParams);
        }

        //var_dump($url . $query);
        return $url . $query;
    }

    public function getClearUrl()
    {
        $url = '';

        $fullActionName = $this->request->getFullActionName();
        switch ($fullActionName) {
            case 'catalog_category_view':
                $url = $this->context->getCurrentCategory()->getUrl();
                break;

            case 'all_products_page_index_index':
                $url = ObjectManager::getInstance()->get('\Mirasvit\AllProducts\Service\UrlService')->getClearUrl();
                break;

            case 'brand_brand_view':
                $url         = ObjectManager::getInstance()->get('\Mirasvit\Brand\Service\BrandUrlService')->getBaseBrandUrl();
                $currentUrl  = $this->request->getRequestString();
                $brandConfig = ObjectManager::getInstance()->get('Mirasvit\Brand\Model\Config\GeneralConfig');

                if ($brandConfig->getFormatBrandUrl() == 1) {
                    $brandRepository = ObjectManager::getInstance()->get('Mirasvit\Brand\Repository\BrandRepository');
                    foreach ($brandRepository->getCollection() as $brand) {
                        if (preg_match('/\/' . $brand->getUrlKey() . '[\/]*\S+/', $currentUrl)) {
                            $url = str_ireplace($brandConfig->getAllBrandUrl(), $brand->getUrlKey(), $url);
                            break;
                        }
                    }
                }

                $path      = parse_url($currentUrl, PHP_URL_PATH);
                $pathParts = explode('/', $path);
                if (isset($pathParts[2])) {
                    $url .= '/' . $pathParts[2]; // pathParts[2] - brand code
                }
                break;
        }

        return $url;
    }

    public function getSuffix()
    {
        $suffix = '';
        if ($this->request->getFullActionName() == 'catalog_category_view') {
            $suffix = $this->urlService->getCategoryUrlSuffix();
        }

        if ($this->request->getFullActionName() == 'brand_brand_view') {
            $suffix = $this->urlService->getBrandUrlSuffix();
        }

        return $suffix;
    }

}
