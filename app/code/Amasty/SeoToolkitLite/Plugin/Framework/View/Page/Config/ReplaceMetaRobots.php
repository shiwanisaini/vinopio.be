<?php

declare(strict_types=1);

namespace Amasty\SeoToolkitLite\Plugin\Framework\View\Page\Config;

use Amasty\SeoToolkitLite\Model\ReplaceMetaRobots as ReplaceRobots;
use Magento\Framework\View\Page\Config as NativeConfig;

class ReplaceMetaRobots
{
    /**
     * @var ReplaceRobots
     */
    private $replaceMetaRobots;

    public function __construct(ReplaceRobots $replaceMetaRobots)
    {
        $this->replaceMetaRobots = $replaceMetaRobots;
    }

    public function afterGetRobots(
        NativeConfig $subject,
        ?string $metaRobots
    ): ?string {
        return $this->replaceMetaRobots->execute($metaRobots);
    }
}
