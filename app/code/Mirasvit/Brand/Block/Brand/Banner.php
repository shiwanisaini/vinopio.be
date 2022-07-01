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



namespace Mirasvit\Brand\Block\Brand;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Brand\Api\Data\BrandPageInterface;
use Mirasvit\Brand\Model\Config\BrandPageConfig;
use Mirasvit\Brand\Model\Config\Config;
use Mirasvit\Brand\Repository\BrandRepository;
use Mirasvit\Brand\Service\BrandPageService;
use Mirasvit\Brand\Service\ImageUrlService;

class Banner extends Template
{
    /**
     * @var string
     */
    protected $_template = 'brand/banner.phtml';

    /**
     * @var array
     */
    protected $bannerLayoutMap
        = [
            BrandPageConfig::BANNER_AFTER_TITLE_POSITION        => BrandPageConfig::BANNER_AFTER_TITLE_POSITION_LAYOUT,
            BrandPageConfig::BANNER_BEFORE_DESCRIPTION_POSITION => BrandPageConfig::BANNER_BEFORE_DESCRIPTION_POSITION_LAYOUT,
            BrandPageConfig::BANNER_AFTER_DESCRIPTION_POSITION  => BrandPageConfig::BANNER_AFTER_DESCRIPTION_POSITION_LAYOUT,
        ];

    private   $brandRepository;

    private   $imageUrlService;

    /**
     * @var Config
     */
    private $config;

    private $brandPageService;

    public function __construct(
        BrandRepository $brandRepository,
        Context $context,
        ImageUrlService $imageUrlService,
        Config $config,
        BrandPageService $brandPageService,
        array $data = []
    ) {
        $this->brandRepository  = $brandRepository;
        $this->imageUrlService  = $imageUrlService;
        $this->config           = $config;
        $this->brandPageService = $brandPageService;

        parent::__construct($context, $data);
    }

    /**
     * @return BrandPageInterface|\Magento\Framework\DataObject|false
     */
    public function getBrandPage()
    {
        return $this->brandPageService->getBrandPage();
    }

    /**
     * @return bool
     */
    public function isCorrectBannerPosition()
    {
        $brandPage = $this->getBrandPage();
        if ($brandPage && ($bannerPosition = $brandPage->getBannerPosition())
            && isset($this->bannerLayoutMap[$bannerPosition])
            && ($this->bannerLayoutMap[$bannerPosition] === $this->getNameInLayout())
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getBannerUrl()
    {
        return $this->imageUrlService->getImageUrl($this->getBrandPage()->getBanner());
    }

    /**
     * @return string
     */
    public function getBannerAlt()
    {
        return ($this->getBrandPage()->getBannerAlt()) ? : $this->getBrandName();
    }

    /**
     * @return string
     */
    public function getBannerTitle()
    {
        return ($this->getBrandPage()->getBannerTitle()) ? : $this->getBrandName();
    }

    /**
     * @return string
     */
    public function getBrandName()
    {
        return $this->brandRepository->get($this->getBrandPage()->getAttributeOptionId())->getLabel();
    }
}
