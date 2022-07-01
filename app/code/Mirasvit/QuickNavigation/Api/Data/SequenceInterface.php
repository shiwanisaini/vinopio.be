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



namespace Mirasvit\QuickNavigation\Api\Data;

interface SequenceInterface
{
    const TABLE_NAME = 'mst_quick_navigation_sequence';

    const ID          = 'sequence_id';
    const STORE_ID    = 'store_id';
    const CATEGORY_ID = 'category_id';
    const SEQUENCE    = 'sequence';
    const LENGTH      = 'length';
    const POPULARITY  = 'popularity';

    public function getId();

    public function getStoreId();

    /**
     * @param int $value
     *
     * @return SequenceInterface
     */
    public function setStoreId($value);

    public function getCategoryId();

    /**
     * @param int $value
     *
     * @return SequenceInterface
     */
    public function setCategoryId($value);

    public function getSequence();

    /**
     * @param string $value
     *
     * @return SequenceInterface
     */
    public function setSequence($value);

    public function getLength();

    /**
     * @param int $value
     *
     * @return SequenceInterface
     */
    public function setLength($value);

    public function getPopularity();

    /**
     * @param int $value
     *
     * @return SequenceInterface
     */
    public function setPopularity($value);
}
