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



namespace Mirasvit\Brand\Service;

use Magento\Framework\DataObject;
use Magento\Framework\Filter\FilterManager;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Brand\Api\Data\BrandPageInterface;
use Mirasvit\Brand\Model\Config\Config;
use Mirasvit\Brand\Repository\BrandRepository;

class BrandUrlService
{
    const LONG_URL  = 0;
    const SHORT_URL = 1;


    private $brandRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var FilterManager
     */
    private $filter;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        BrandRepository $brandRepository,
        Config $config,
        FilterManager $filter
    ) {
        $this->brandRepository = $brandRepository;
        $this->config          = $config;
        $this->filter          = $filter;
        $this->storeManager    = $storeManager;
    }

    /**
     * @param int $storeId
     *
     * @return string
     */
    public function getBaseBrandUrl($storeId = 0)
    {
        if ($storeId) {
            return $this->storeManager->getStore($storeId)->getBaseUrl() . $this->getBaseRoute(true, $storeId);
        }

        return $this->storeManager->getStore()->getBaseUrl() . $this->getBaseRoute(true);
    }

    /**
     * @param string $urlKey
     * @param string $brandTitle
     * @param int    $storeId
     *
     * @return string
     */
    public function getBrandUrl($urlKey, $brandTitle, $storeId = 0)
    {
        if (!$urlKey) {
            $urlKey = $this->filter->translitUrl($brandTitle);
        }

        $formatBrandUrl = $this->config->getGeneralConfig()->getFormatBrandUrl();
        if ($formatBrandUrl === self::SHORT_URL) {
            $brandUrl = $urlKey;
        } else {
            $brandUrl = $this->getBaseRoute(false, $storeId) . '/' . $urlKey;
        }

        return $brandUrl . $this->config->getGeneralConfig()->getUrlSuffix();
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @param string $pathInfo
     *
     * @return false|DataObject
     */
    public function match($pathInfo)
    {
        $identifier = trim($pathInfo, '/');
        $parts      = explode('/', $identifier);

        $brandUrlKeys = $this->getAvailableBrandUrlKeys();

        if ($parts[0] !== $this->getBaseRoute() && !in_array($parts[0], $brandUrlKeys, true)) {
            return false;
        }

        $formatBrandUrl = $this->config->getGeneralConfig()->getFormatBrandUrl();

        if (count($parts) === 1 && ($formatBrandUrl === self::SHORT_URL || $parts[0] === $this->getBaseRoute(true))) {
            $urlKey = $parts[0];
        } elseif ($formatBrandUrl === self::LONG_URL && isset($parts[1])) {
            $parts[1] = str_replace($this->config->getGeneralConfig()->getUrlSuffix(), '', $parts[1]);
            $urlKey   = implode('/', [$parts[0], $parts[1]]) . $this->config->getGeneralConfig()->getUrlSuffix();
        } else {
            return false;
        }

        if ($urlKey === $this->getBaseRoute(true)) {
            return new DataObject([
                'module_name'     => 'brand',
                'controller_name' => 'brand',
                'action_name'     => 'index',
                'route_name'      => $urlKey,
                'params'          => [],
            ]);
        } elseif (in_array($urlKey, $brandUrlKeys, true)) {
            $optionId = array_search($urlKey, $brandUrlKeys, true);

            return new DataObject([
                'module_name'     => 'brand',
                'controller_name' => 'brand',
                'action_name'     => 'view',
                'route_name'      => $brandUrlKeys[$optionId],
                'params'          => [BrandPageInterface::ATTRIBUTE_OPTION_ID => $optionId],
            ]);
        }

        return false;
    }

    /**
     * @param bool $withSuffix - add Brand URL suffix or not
     * @param int  $storeId
     *
     * @return string
     */
    private function getBaseRoute($withSuffix = false, $storeId = 0)
    {
        $baseRoute = $this->config->getGeneralConfig()->getAllBrandUrl($storeId);

        if ($withSuffix) {
            $baseRoute .= $this->config->getGeneralConfig()->getUrlSuffix();
        }

        return $baseRoute;
    }

    /**
     * @return string[]
     */
    private function getAvailableBrandUrlKeys()
    {
        $urlKeys = [$this->getBaseRoute(true)];

        $brandPages = $this->brandRepository->getCollection();

        foreach ($brandPages as $brand) {
            $urlKeys[$brand->getId()] = $brand->getUrl();
        }

        return $urlKeys;
    }
}
