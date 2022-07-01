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



namespace Mirasvit\SeoFilter\Plugin\Frontend;

use Mirasvit\SeoFilter\Model\Config;
use Mirasvit\SeoFilter\Service\FriendlyUrlService;
use Mirasvit\SeoFilter\Service\UrlService;

/**
 * @see \Magento\Swatches\Block\LayeredNavigation\RenderLayered::buildUrl()
 */
class AddSwatchUrlParamsPlugin
{
    private $friendlyUrlService;

    private $urlService;

    private $config;

    public function __construct(
        FriendlyUrlService $friendlyUrlService,
        UrlService $urlService,
        Config $config
    ) {
        $this->friendlyUrlService = $friendlyUrlService;
        $this->urlService         = $urlService;
        $this->config             = $config;
    }

    //    /**
    //     * @param object   $subject
    //     * @param \Closure $proceed
    //     * @param string   $attributeCode
    //     * @param int      $optionId
    //     *
    //     * @return string
    //     */
    //    public function aroundGetRemoveUrl($subject, $proceed, $attributeCode, $optionId)
    //    {
    //        if (!$this->config->isApplicable()) {
    //            return $proceed($attributeCode, $optionId);
    //        }
    //
    //        $url = $this->friendlyUrlService->getUrl($attributeCode, $optionId, true);
    //
    //        return $this->urlService->addUrlParams($url);
    //    }

    /**
     * @param object   $subject
     * @param \Closure $proceed
     * @param string   $attributeCode
     * @param int      $optionId
     *
     * @return string
     */
    public function aroundBuildUrl($subject, $proceed, $attributeCode, $optionId)
    {
        if (!$this->config->isApplicable()) {
            return $proceed($attributeCode, $optionId);
        }

        $url = $this->friendlyUrlService->getUrl($attributeCode, $optionId);

        return $url;//$this->urlService->addUrlParams($url);
    }
}
