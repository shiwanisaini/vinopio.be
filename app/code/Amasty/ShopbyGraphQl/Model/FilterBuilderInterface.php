<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Improved Layered Navigation GraphQl for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\ShopbyGraphQl\Model;

interface FilterBuilderInterface
{
    public function build(array &$filters, int $storeId): void;
}
