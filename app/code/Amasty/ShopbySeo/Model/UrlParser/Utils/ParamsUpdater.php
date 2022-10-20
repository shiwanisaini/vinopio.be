<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Shop by Seo for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\ShopbySeo\Model\UrlParser\Utils;

class ParamsUpdater
{
    public function update(array &$params, string $paramName, string $value): void
    {
        if (array_key_exists($paramName, $params)) {
            $params[$paramName] .= ',' . $value;
        } else {
            $params[$paramName] = '' . $value;
        }
    }
}
