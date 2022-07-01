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

class BrandPageConfig extends BaseConfig
{
    const BRAND_ATTRIBUTE     = 'BrandAttribute';
    const ATTRIBUTE_OPTION_ID = 'AttributeOptionId';
    const BRAND_URL_KEY       = 'BrandUrlKey';
    const BRAND_DEFAULT_NAME  = 'BrandDefaultName';
    const BRAND_DATA          = 'm__BrandData';
    const BRAND_PAGE_ID       = 'BrandPageId';

    const INDEX_FOLLOW     = 'INDEX, FOLLOW';
    const NOINDEX_FOLLOW   = 'NOINDEX, FOLLOW';
    const INDEX_NOFOLLOW   = 'INDEX, NOFOLLOW';
    const NOINDEX_NOFOLLOW = 'NOINDEX, NOFOLLOW';

    const BANNER_AFTER_TITLE_POSITION        = 'After title position';
    const BANNER_BEFORE_DESCRIPTION_POSITION = 'Before description position';
    const BANNER_AFTER_DESCRIPTION_POSITION  = 'After description position';

    const BANNER_AFTER_TITLE_POSITION_LAYOUT        = 'm.brand.banner.after_title';
    const BANNER_BEFORE_DESCRIPTION_POSITION_LAYOUT = 'm.brand.banner.before_description';
    const BANNER_AFTER_DESCRIPTION_POSITION_LAYOUT  = 'm.brand.banner.after_description';


    /**
     * {@inheritdoc}
     */
    public function isShowBrandLogo()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_page/isShowBrandLogo',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isShowBrandDescription()
    {
        return $this->scopeConfig->getValue(
            'brand/brand_page/isShowBrandDescription',
            ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }
}
