<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Shop by Base for Magento 2 (System)
*/

namespace Amasty\ShopbyBase\Api;

interface UrlBuilderInterface
{
    /**
     * @param null $routePath
     * @param null $routeParams
     * @return string
     */
    public function getUrl($routePath = null, $routeParams = null);

    /**
     * @param bool $modified = true
     * @return string
     */
    public function getCurrentUrl($modified = true);

    /**
     * @param array $params
     * @return string|null
     */
    public function getBaseUrl($params = []);
}
