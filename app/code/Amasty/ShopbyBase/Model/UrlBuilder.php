<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Shop by Base for Magento 2 (System)
*/

namespace Amasty\ShopbyBase\Model;

use Amasty\ShopbyBase\Api\UrlBuilderInterface;
use Amasty\ShopbyBase\Api\UrlBuilder\AdapterInterface;
use Amasty\ShopbyBase\Api\UrlModifierInterface;

class UrlBuilder implements UrlBuilderInterface
{
    public const DEFAULT_ORDER = 100;

    /**
     * @var array
     */
    private $storage = [];

    /**
     * @var AdapterInterface[]
     */
    private $urlAdapters = [];

    /**
     * @var UrlModifierInterface[]
     */
    private $urlModifiers = [];

    public function __construct(
        $urlAdapters = [],
        $urlModifiers = []
    ) {
        $this->initAdapters($urlAdapters);
        $this->initModifiers($urlModifiers);
    }

    /**
     * @param null|string $routePath
     * @param null|array $routeParams
     * @return string|null
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        $key = $this->getKey($routePath, $routeParams);
        if (!isset($this->storage[$key])) {
            $url = null;
            foreach ($this->urlAdapters as $adapter) {
                if ($url = $adapter->getUrl($routePath, $routeParams)) {
                    break;
                }
            }

            foreach ($this->urlModifiers as $modifier) {
                $url = $modifier->modifyUrl($url);
            }

            $this->storage[$key] = $url;
        }

        return $this->storage[$key];
    }

    private function getKey(?string $routePath = null, ?array $routeParams = null): string
    {
        $key = '' . $routePath;
        if ($routeParams !== null) {
            $key .= json_encode($routeParams);
        }

        return $key;
    }

    /**
     * @param bool $modified = true
     * @return string|null
     */
    public function getCurrentUrl($modified = true)
    {
        $url = null;
        foreach ($this->urlAdapters as $adapter) {
            if (method_exists($adapter, 'getCurrentUrl')) {
                $url = $adapter->getCurrentUrl();
                break;
            }
        }

        if ($modified) {
            foreach ($this->urlModifiers as $modifier) {
                $url = $modifier->modifyUrl($url);
            }
        }

        return $url;
    }

    /**
     * @param array $params
     * @return string|null
     */
    public function getBaseUrl($params = [])
    {
        $url = null;
        foreach ($this->urlAdapters as $adapter) {
            if (method_exists($adapter, 'getBaseUrl')) {
                $url = $adapter->getBaseUrl($params);
                break;
            }
        }

        return $url;
    }

    /**
     * @param array $urlAdapters
     * @return $this
     */
    private function initAdapters(array $urlAdapters = [])
    {
        foreach ($urlAdapters as $urlAdapter) {
            if (isset($urlAdapter['adapter'])
                && ($urlAdapter['adapter'] instanceof AdapterInterface)
            ) {
                $order = isset($urlAdapter['sort_order']) ? $urlAdapter['sort_order'] : self::DEFAULT_ORDER;
                $this->urlAdapters[$order] = $urlAdapter['adapter'];
            }
        }
        ksort($this->urlAdapters, SORT_NUMERIC);
        return $this;
    }

    /**
     * @param array $urlModifiers
     * @return $this
     */
    private function initModifiers(array $urlModifiers = [])
    {
        foreach ($urlModifiers as $urlModifier) {
            if (isset($urlModifier['adapter'])
                && ($urlModifier['adapter'] instanceof UrlModifierInterface)
            ) {
                $order = isset($urlModifier['sort_order']) ? $urlModifier['sort_order'] : self::DEFAULT_ORDER;
                $this->urlModifiers[$order] = $urlModifier['adapter'];
            }
        }
        ksort($this->urlModifiers, SORT_NUMERIC);
        return $this;
    }
}
