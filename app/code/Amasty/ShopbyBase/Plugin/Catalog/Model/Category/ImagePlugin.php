<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Shop by Base for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\ShopbyBase\Plugin\Catalog\Model\Category;

use Amasty\ShopbyBase\Model\Category\Manager as CategoryManager;
use Magento\Catalog\Model\Category;

class ImagePlugin
{
    /**
     * @param \Magento\Catalog\Model\Category\Image $subject
     * @param string $result
     * @param Category $category
     * @return string
     */
    public function afterGetUrl(
        $subject,
        string $result,
        Category $category
    ): string {
        $image = $category->getData(CategoryManager::CATEGORY_SHOPBY_IMAGE_URL);

        return $image ?: $result;
    }
}
