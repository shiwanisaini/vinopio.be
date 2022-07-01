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



namespace Mirasvit\LayeredNavigation\Api\Data;

use Mirasvit\LayeredNavigation\Model\AttributeConfig\OptionConfig;

interface AttributeConfigInterface
{
    const TABLE_NAME = 'mst_navigation_attribute_config';

    const DISPLAY_MODE_LABEL          = 'label';
    const DISPLAY_MODE_SLIDER         = 'slider';
    const DISPLAY_MODE_FROM_TO        = 'from-to';
    const DISPLAY_MODE_SLIDER_FROM_TO = self::DISPLAY_MODE_SLIDER . '+' . self::DISPLAY_MODE_FROM_TO;
    const DISPLAY_MODE_RANGE          = 'range';
    const DISPLAY_MODE_DROPDOWN       = 'dropdown';

    const CATEGORY_VISIBILITY_MODE_ALL              = 'all';
    const CATEGORY_VISIBILITY_MODE_SHOW_IN_SELECTED = 'show_in_selected';
    const CATEGORY_VISIBILITY_MODE_HIDE_IN_SELECTED = 'hide_in_selected';

    const OPTION_SORT_BY_POSITION = 'position';
    const OPTION_SORT_BY_LABEL    = 'label';

    const ID             = 'config_id';
    const ATTRIBUTE_ID   = 'attribute_id';
    const ATTRIBUTE_CODE = 'attribute_code';
    const CONFIG         = 'config';

    const OPTIONS_CONFIG = 'options';

    const OPTIONS_SORT_BY = 'options_sort_by';

    const DISPLAY_MODE   = 'display_mode';
    const VALUE_TEMPLATE = 'value_template';

    const IS_SHOW_SEARCH_BOX = 'is_show_search_box';

    const CATEGORY_VISIBILITY_MODE = 'category_visibility_mode';
    const CATEGORY_VISIBILITY_IDS  = 'category_visibility_ids';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getAttributeId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setAttributeId($value);

    /**
     * @return string
     */
    public function getAttributeCode();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAttributeCode($value);

    /**
     * @return array
     */
    public function getConfig();

    /**
     * @param array $value
     *
     * @return $this
     */
    public function setConfig(array $value);

    /**
     * @return OptionConfig[]
     */
    public function getOptionsConfig();

    /**
     * @param OptionConfig[] $value
     *
     * @return $this
     */
    public function setOptionsConfig(array $value);

    /**
     * @return string
     */
    public function getCategoryVisibilityMode();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCategoryVisibilityMode($value);

    /**
     * @return array
     */
    public function getCategoryVisibilityIds();

    /**
     * @param array $value
     *
     * @return $this
     */
    public function setCategoryVisibilityIds(array $value);

    /**
     * @return string
     */
    public function getOptionsSortBy();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOptionsSortBy($value);

    /**
     * @return string
     */
    public function getDisplayMode();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDisplayMode($value);

    /**
     * @return string
     */
    public function getValueTemplate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValueTemplate($value);

    /**
     * @return bool
     */
    public function isShowSearchBox();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIsShowSearchBox($value);
}
