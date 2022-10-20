<?php

declare(strict_types=1);

namespace Amasty\SeoToolkitLite\Model\Redirect\Command;

interface DeleteExpiredRedirectsInterface
{
    /**
     * @return void
     */
    public function execute(): void;
}
