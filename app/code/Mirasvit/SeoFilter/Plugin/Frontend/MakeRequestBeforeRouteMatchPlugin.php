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

use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Mirasvit\SeoFilter\Model\Config;
use Mirasvit\SeoFilter\Service\ParserService;

/**
 * @see \Magento\Framework\App\Router\Base::match()
 */
class MakeRequestBeforeRouteMatchPlugin
{
    private $config;

    private $parserService;

    private $url;

    public function __construct(
        UrlInterface $url,
        ParserService $parserService,
        Config $config
    ) {
        $this->url           = $url;
        $this->parserService = $parserService;
        $this->config        = $config;
    }

    /**
     * Apply friendly filters
     *
     * @param object           $subject
     * @param RequestInterface $request
     *
     * @return void
     */
    public function beforeMatch($subject, RequestInterface $request)
    {
        if ($this->config->isEnabled()) {
            $params = $this->parserService->getParams();

            if ($params && $params['match']) {
                /** @var \Magento\Framework\App\Request\Http $request */

                if ($params['is_brand_page'] || $params['is_all_pages']) {
                    $request->setParams($params['params']);
                }

                if ($params['category_id']) {
                    $request->setRouteName('catalog')
                        ->setModuleName('catalog')
                        ->setControllerName('category')
                        ->setActionName('view')
                        ->setParam('id', $params['category_id'])
                        ->setParams($params['params']);
                }
            }
        }
    }
}
