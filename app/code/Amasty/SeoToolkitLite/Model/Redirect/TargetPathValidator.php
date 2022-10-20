<?php

declare(strict_types=1);

namespace Amasty\SeoToolkitLite\Model\Redirect;

use Laminas\Uri\Http;

class TargetPathValidator
{
    /**
     * @var Http
     */
    private $http;

    public function __construct(
        Http $http
    ) {
        $this->http = $http;
    }
    
    /**
     * @param string $targetPath
     * @return bool
     */
    public function isTargetPathExternal(string $targetPath): bool
    {
        $parsedUrl = $this->http->parse($targetPath);

        return (bool)$parsedUrl->getScheme();
    }
}
