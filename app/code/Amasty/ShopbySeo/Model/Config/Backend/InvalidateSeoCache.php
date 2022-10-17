<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Shop by Seo for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\ShopbySeo\Model\Config\Backend;

use Amasty\ShopbySeo\Model\SeoOptions;

class InvalidateSeoCache extends \Magento\Framework\App\Config\Value
{
    /**
     * Processing object after save data
     *
     * @return \Magento\Framework\App\Config\Value
     */
    public function afterSave()
    {
        if ($this->isValueChanged()) {
            $this->cacheTypeList->invalidate(SeoOptions::CACHE_KEY);
        }

        return parent::afterSave();
    }
}
