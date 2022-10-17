<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Grouped Options for Magento 2
*/

declare(strict_types=1);

namespace Amasty\GroupedOptions\Model\ResourceModel;

use Amasty\GroupedOptions\Api\Data\GroupAttrOptionInterface;
use Amasty\GroupedOptions\Api\GroupRepositoryInterface;

class GroupAttrOption extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init(GroupRepositoryInterface::TABLE_OPTIONS, GroupAttrOptionInterface::ID);
    }
}
