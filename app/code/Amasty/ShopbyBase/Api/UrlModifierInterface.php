<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Shop by Base for Magento 2 (System)
*/

namespace Amasty\ShopbyBase\Api;

interface UrlModifierInterface
{
    /**
     * @param string $url
     * @return string
     */
    public function modifyUrl($url);
}
