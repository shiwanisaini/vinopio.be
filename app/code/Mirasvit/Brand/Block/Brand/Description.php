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
use Mirasvit\Brand\Model\Config\Config;
use Mirasvit\Brand\Service\BrandPageService;
use Mirasvit\Brand\Service\ImageUrlService;

class Description extends Template
{
    /**
     * @var string
     */
    protected $_template = 'brand/description.phtml';

    private   $brandPageService;

    private   $imageUrlService;

    private   $config;

    public function __construct(
        Context $context,
        BrandPageService $brandPageService,
        ImageUrlService $imageUrlService,
        Config $config,
        array $data = []
    ) {
        $this->brandPageService = $brandPageService;
        $this->imageUrlService  = $imageUrlService;
        $this->config           = $config;

        parent::__construct($context, $data);
    }

    public function getBrandPage()
    {
        return $this->brandPageService->getBrandPage();
    }

    /**
     * @return string
     */
    public function getBrandLogoUrl()
    {
        return $this->imageUrlService->getImageUrl($this->getBrandPage()->getLogo());
    }

    /**
     * @return bool
     */
    public function isShowBrandLogo()
    {
        return $this->config->getBrandPageConfig()->isShowBrandLogo();
    }

    /**
     * @return bool
     */
    public function isShowBrandDescription()
    {
        return $this->config->getBrandPageConfig()->isShowBrandDescription();
    }
}
