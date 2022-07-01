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

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Result\Page;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Brand\Model\Config\BrandPageConfig;
use Mirasvit\Brand\Model\Config\Config;
use Mirasvit\Brand\Repository\BrandPageRepository;

class BrandPageMetaService
{
    private $brandUrlService;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    private $brandPageRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Context
     */
    private $context;

    public function __construct(
        BrandUrlService $brandUrlService,
        Registry $registry,
        BrandPageRepository $brandPageRepository,
        Config $config,
        Context $context
    ) {
        $this->brandUrlService     = $brandUrlService;
        $this->registry            = $registry;
        $this->storeManager        = $context->getStoreManager();
        $this->brandPageRepository = $brandPageRepository;
        $this->config              = $config;
        $this->context             = $context;
    }

    /**
     * @param bool $isIndexPage
     *
     * @return string
     */
    public function getTitle($isIndexPage)
    {
        if ($isIndexPage) {
            return ' ';
        }
        $brandPageData = $this->getDefaultData();
        if (($brandPageId = $this->getBrandPageId($brandPageData))
            && ($brandTitle = $this->brandPageRepository->get($brandPageId)->getBrandTitle())) {
            return $brandTitle;
        }

        return $brandPageData[BrandPageConfig::BRAND_DEFAULT_NAME];
    }

    /**
     * @param bool $isIndexPage
     *
     * @return string
     */
    public function getMetaTitle($isIndexPage)
    {
        if ($isIndexPage) {
            return ($this->config->getAllBrandPageConfig()->getMetaTitle()) ? : __('Brands');
        }
        $brandPageData = $this->getDefaultData();
        if (($brandPageId = $this->getBrandPageId($brandPageData))
            && ($metaTitle = $this->brandPageRepository->get($brandPageId)->getMetaTitle())) {
            return $metaTitle;
        }

        return $brandPageData[BrandPageConfig::BRAND_DEFAULT_NAME];
    }

    /**
     * @param bool $isIndexPage
     *
     * @return string
     */
    public function getKeyword($isIndexPage)
    {
        if ($isIndexPage) {
            return $this->config->getAllBrandPageConfig()->getMetaKeyword();
        }
        $brandPageData = $this->getDefaultData();
        if (($brandPageId = $this->getBrandPageId($brandPageData))
            && ($metaKeyword = $this->brandPageRepository->get($brandPageId)->getMetaKeyword())) {
            return $metaKeyword;
        }

        return $brandPageData[BrandPageConfig::BRAND_DEFAULT_NAME];
    }

    /**
     * @param bool $isIndexPage
     *
     * @return string
     */
    public function getMetaDescription($isIndexPage)
    {
        if ($isIndexPage) {
            return $this->config->getAllBrandPageConfig()->getMetaDescription();
        }
        $brandPageData = $this->getDefaultData();
        if (($brandPageId = $this->getBrandPageId($brandPageData))
            && ($metaDescription = $this->brandPageRepository->get($brandPageId)->getMetaDescription())) {
            return $metaDescription;
        }

        return $brandPageData[BrandPageConfig::BRAND_DEFAULT_NAME];
    }

    /**
     * @param bool $isIndexPage
     *
     * @return string
     */
    public function getCanonical($isIndexPage)
    {
        if ($isIndexPage) {
            return $this->brandUrlService->getBaseBrandUrl();
        }
        $brandPageData = $this->getDefaultData();
        if (($brandPageId = $this->getBrandPageId($brandPageData))
            && ($canonical = $this->brandPageRepository->get($brandPageId)->getCanonical())) {
            if ((strpos('http:', $canonical) !== false) && (strpos('https:', $canonical) !== false)) {
                return $canonical;
            } else {
                return $this->storeManager->getStore()->getBaseUrl() . ltrim($canonical, '/');
            }
        }

        return $this->storeManager->getStore()->getBaseUrl()
            . $this->getDefaultData()[BrandPageConfig::BRAND_URL_KEY];
    }

    /**
     * @param bool $isIndexPage
     *
     * @return string
     */
    public function getRobots($isIndexPage)
    {
        $indexFollow = 'INDEX,FOLLOW';
        if ($isIndexPage) {
            return $indexFollow;
        }

        $brandPageData = $this->getDefaultData();
        if (($brandPageId = $this->getBrandPageId($brandPageData))
            && ($robots = $this->brandPageRepository->get($brandPageId)->getRobots())) {
            return $robots;
        }

        return $indexFollow;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return $this->registry->registry(BrandPageConfig::BRAND_DATA);
    }

    /**
     * @param array $brandPageData
     *
     * @return int|null
     */
    public function getBrandPageId($brandPageData)
    {
        $brandPageId = null;
        if (isset($brandPageData[BrandPageConfig::BRAND_PAGE_ID])) {
            $brandPageId = $brandPageData[BrandPageConfig::BRAND_PAGE_ID];
        }

        return $brandPageId;
    }

    /**
     * @param Page $page
     * @param bool $isIndexPage
     *
     * @return Page
     */
    public function apply(Page $page, $isIndexPage = false)
    {
        $pageConfig = $page->getConfig();
        $pageConfig->getTitle()->set(__($this->getMetaTitle($isIndexPage)));
        $pageConfig->setMetadata('description', $this->getMetaDescription($isIndexPage));
        $pageConfig->setMetadata('keyword', $this->getKeyword($isIndexPage));
        $pageConfig->setMetadata('robots', $this->getRobots($isIndexPage));
        $pageConfig->addRemotePageAsset(
            $this->getCanonical($isIndexPage),
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );
        $layout = $this->context->getLayout();
        if ($pageMainTitle = $layout->getBlock('page.main.title')) {
            $pageMainTitle->setPageTitle($this->getTitle($isIndexPage));
        }

        return $page;
    }
}
