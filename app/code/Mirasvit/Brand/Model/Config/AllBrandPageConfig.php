<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-navigation
 * @version   1.1.2
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Brand\Model\Config;

use Magento\Store\Model\ScopeInterface;

class AllBrandPageConfig extends BaseConfig
{
    /**
     * {@inheritdoc}
     */
    public function isShowBrandLogo()
    {
        return $this->scopeConfig->getValue(
            'brand/all_brand_page/isShowBrandLogo',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        return $this->scopeConfig->getValue(
            'brand/all_brand_page/MetaTitle',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeyword()
    {
        return $this->scopeConfig->getValue(
            'brand/all_brand_page/MetaKeyword',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->scopeConfig->getValue(
            'brand/all_brand_page/MetaDescription',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }
}
