<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Shop by Base for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\ShopbyBase\Model\Integration\Shopby;

class IsBrandPage
{
    /**
     * @var array
     */
    private $data;

    public function __construct(
        array $data = []
    ) {
        $this->data = $data;
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        return isset($this->data['object']) ? $this->data['object']->execute() : false;
    }
}
