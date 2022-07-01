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



namespace Mirasvit\QuickNavigation\Model;

use Magento\Framework\Model\AbstractModel;
use Mirasvit\QuickNavigation\Api\Data\SequenceInterface;

class Sequence extends AbstractModel implements SequenceInterface
{
    public function getId()
    {
        return (int)$this->getData(self::ID);
    }

    public function getStoreId()
    {
        return (int)$this->getData(self::STORE_ID);
    }

    public function setStoreId($value)
    {
        return $this->setData(self::STORE_ID, $value);
    }

    public function getCategoryId()
    {
        return (int)$this->getData(self::CATEGORY_ID);
    }

    public function setCategoryId($value)
    {
        return $this->setData(self::CATEGORY_ID, $value);
    }

    public function getSequence()
    {
        return (string)$this->getData(self::SEQUENCE);
    }

    public function setSequence($value)
    {
        return $this->setData(self::SEQUENCE, $value);
    }

    public function getLength()
    {
        return (int)$this->getData(self::LENGTH);
    }

    public function setLength($value)
    {
        return $this->setData(self::LENGTH, $value);
    }

    public function getPopularity()
    {
        return (int)$this->getData(self::POPULARITY);
    }

    public function setPopularity($value)
    {
        return $this->setData(self::POPULARITY, $value);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\Sequence::class);
    }
}
