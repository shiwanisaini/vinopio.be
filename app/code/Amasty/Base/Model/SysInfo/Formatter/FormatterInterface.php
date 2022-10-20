<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Magento 2 Base Package
*/
declare(strict_types=1);

namespace Amasty\Base\Model\SysInfo\Formatter;

interface FormatterInterface
{
    public function getContent(): string;

    public function getExtension(): string;
}
