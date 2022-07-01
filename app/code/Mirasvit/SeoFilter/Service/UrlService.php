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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\SeoFilter\Model\Context;

class UrlService
{
    /**
     * Cache for category rewrite suffix
     * @var array
     */
    private $categoryUrlSuffix = [];

    private $scopeConfig;

    private $storeManager;

    private $registry;

    private $context;

    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Registry $registry,
        Context $context
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig  = $scopeConfig;
        $this->registry     = $registry;
        $this->context      = $context;
    }

    /**
     * Retrieve category rewrite suffix for store
     *
     * @param null|int $storeId
     *
     * @return string
     */
    public function getCategoryUrlSuffix($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        if (!isset($this->categoryUrlSuffix[$storeId])) {
            $this->categoryUrlSuffix[$storeId] = $this->scopeConfig->getValue(
                \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        return $this->categoryUrlSuffix[$storeId];
    }

    /**
     * Retrieve category rewrite suffix for store
     *
     * @param null|int $storeId
     *
     * @return string
     */
    public function getBrandUrlSuffix($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $this->scopeConfig->getValue(
            'brand/general/url_suffix', ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param string $url
     *
     * @return string|string[]
     */
    public function trimCategorySuffix($url)
    {
        $suffix = $this->getCategoryUrlSuffix();

        if ($suffix && $suffix !== '/') {
            $url = str_replace($suffix, '', $url);
        }

        return $url;
    }

    /**
     * Return catalog current category object
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * @param bool|string $url
     *
     * @return string
     */
    public function getQueryParams($url = false)
    {
        $currentUrl = $this->context->getUrlBuilder()->getCurrentUrl();

        if ($url) {
            return strtok($currentUrl, '?') . strstr($url, '?', false);
        }

        return strstr($currentUrl, '?', false);
    }

    public function getGetParams()
    {
        $currentUrl = $this->context->getUrlBuilder()->getCurrentUrl();

        $params = [];
        parse_str(parse_url($currentUrl, PHP_URL_QUERY), $params);

        return $params;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function addUrlParams($url)
    {
        return $this->mergeGetParams(
            $url,
            $this->context->getUrlBuilder()->getCurrentUrl()
        );
    }

    /**
     * @param string $urlA
     * @param string $urlB
     *
     * @return string $urlA + GET($urlA) + GET($urlB)
     */
    private function mergeGetParams($urlA, $urlB)
    {
        $aParams = [];
        parse_str(parse_url($urlA, PHP_URL_QUERY), $aParams);

        $bParams = [];
        parse_str(parse_url($urlB, PHP_URL_QUERY), $bParams);

        foreach ($aParams as $key => $value) {
            $bParams[$key] = $value;
        }

        $query = '';

        if (count($bParams)) {
            $query = '?' . http_build_query($bParams);
        }

        return strtok($urlA, '?') . $query;
    }
}
