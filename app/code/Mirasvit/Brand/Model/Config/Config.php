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



namespace Mirasvit\Brand\Model\Config;

class Config
{
    /**
     * @var BrandLogoConfig
     */
    private $brandLogoConfig;
    /**
     * @var MoreFromBrandConfig
     */
    private $moreFromBrandConfig;
    /**
     * @var BrandSliderConfig
     */
    private $brandSliderConfig;
    /**
     * @var AllBrandPageConfig
     */
    private $allBrandPageConfig;
    /**
     * @var BrandPageConfig
     */
    private $brandPageConfig;
    /**
     * @var GeneralConfig
     */
    private $generalConfig;

    public function __construct(
        GeneralConfig $generalConfig,
        BrandPageConfig $brandPageConfig,
        AllBrandPageConfig $allBrandPageConfig,
        BrandSliderConfig $brandSliderConfig,
        MoreFromBrandConfig $moreFromBrandConfig,
        BrandLogoConfig $brandLogoConfig
    ) {
        $this->generalConfig       = $generalConfig;
        $this->brandPageConfig     = $brandPageConfig;
        $this->allBrandPageConfig  = $allBrandPageConfig;
        $this->brandSliderConfig   = $brandSliderConfig;
        $this->moreFromBrandConfig = $moreFromBrandConfig;
        $this->brandLogoConfig     = $brandLogoConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getGeneralConfig()
    {
        return $this->generalConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandPageConfig()
    {
        return $this->brandPageConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllBrandPageConfig()
    {
        return $this->allBrandPageConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandSliderConfig()
    {
        return $this->brandSliderConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getMoreFromBrandConfig()
    {
        return $this->moreFromBrandConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getBrandLogoConfig()
    {
        return $this->brandLogoConfig;
    }
}
