<?php

declare(strict_types=1);

namespace Amasty\SeoToolkitLite\Plugin\Catalog\Model\Product;

use Amasty\SeoToolkitLite\Model\Redirect\Product\ProcessBeforeDeletion;
use Magento\Catalog\Model\Product;

class CreateRedirects
{
    /**
     * @var ProcessBeforeDeletion
     */
    private $processBeforeDeletion;
    
    public function __construct(
        ProcessBeforeDeletion $processBeforeDeletion
    ) {
        $this->processBeforeDeletion = $processBeforeDeletion;
    }

    /**
     * @see Product::beforeDelete()
     *
     * @param Product $product
     * @return void
     */
    public function beforeBeforeDelete(Product $product): void
    {
        foreach ($product->getStoreIds() as $storeId) {
            $this->processBeforeDeletion->execute((int)$product->getEntityId(), (int)$storeId);
        }
    }
}
