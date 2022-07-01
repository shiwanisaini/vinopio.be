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



namespace Mirasvit\LayeredNavigation\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\LayeredNavigation\Api\Data\AttributeConfigInterface;
use Mirasvit\LayeredNavigation\Model\AttributeConfig\OptionConfig;

class AttributeConfig extends AbstractModel implements AttributeConfigInterface
{
    protected function _construct()
    {
        $this->_init(ResourceModel\AttributeConfig::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getAttributeId()
    {
        return $this->getData(self::ATTRIBUTE_ID);
    }

    public function setAttributeId($value)
    {
        return $this->setData(self::ATTRIBUTE_ID, $value);
    }

    public function getAttributeCode()
    {
        return $this->getData(self::ATTRIBUTE_CODE);
    }

    public function setAttributeCode($value)
    {
        return $this->setData(self::ATTRIBUTE_CODE, $value);
    }

    public function getConfig()
    {
        $value = $this->getData(self::CONFIG);

        try {
            return \Zend_Json::decode($value);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function setConfig(array $value)
    {
        return $this->setData(self::CONFIG, \Zend_Json::encode($value));
    }

    public function getOptionsConfig()
    {
        $value = $this->getConfigData(self::OPTIONS_CONFIG, []);

        $options = [];
        foreach ($value as $data) {
            $options[] = new OptionConfig($data);
        }

        return $options;
    }

    public function setOptionsConfig(array $value)
    {
        $options = [];
        foreach ($value as $item) {
            $options[] = $item->getData();
        }

        return $this->setConfigData(self::OPTIONS_CONFIG, $options);
    }

    public function getCategoryVisibilityMode()
    {
        return $this->getConfigData(self::CATEGORY_VISIBILITY_MODE, self::CATEGORY_VISIBILITY_MODE_ALL);
    }

    public function setCategoryVisibilityMode($value)
    {
        return $this->setConfigData(self::CATEGORY_VISIBILITY_MODE, $value);
    }

    public function getCategoryVisibilityIds()
    {
        return $this->getConfigData(self::CATEGORY_VISIBILITY_IDS, []);
    }

    public function setCategoryVisibilityIds(array $value)
    {
        return $this->setConfigData(self::CATEGORY_VISIBILITY_IDS, $value);
    }

    public function getOptionsSortBy()
    {
        return $this->getConfigData(self::OPTIONS_SORT_BY, self::OPTION_SORT_BY_POSITION);
    }

    public function setOptionsSortBy($value)
    {
        return $this->setConfigData(self::OPTIONS_SORT_BY, $value);
    }

    public function getDisplayMode()
    {
        return $this->getConfigData(self::DISPLAY_MODE, self::DISPLAY_MODE_LABEL);
    }

    public function setDisplayMode($value)
    {
        return $this->setConfigData(self::DISPLAY_MODE, $value);
    }

    public function getValueTemplate()
    {
        return $this->getConfigData(self::VALUE_TEMPLATE);
    }

    public function setValueTemplate($value)
    {
        return $this->setConfigData(self::VALUE_TEMPLATE, $value);
    }

    public function isShowSearchBox()
    {
        return $this->getConfigData(self::IS_SHOW_SEARCH_BOX);
    }

    public function setIsShowSearchBox($value)
    {
        return $this->setConfigData(self::IS_SHOW_SEARCH_BOX, $value);
    }

    /**
     * @param string      $key
     * @param null|string $default
     *
     * @return mixed|null
     */
    private function getConfigData($key, $default = null)
    {
        $config = $this->getConfig();

        return isset($config[$key]) ? $config[$key] : $default;
    }

    /**
     * @param string       $key
     * @param string|mixed $value
     *
     * @return AttributeConfigInterface|AttributeConfig
     */
    private function setConfigData($key, $value)
    {
        $config       = $this->getConfig();
        $config[$key] = $value;

        return $this->setConfig($config);
    }
}
