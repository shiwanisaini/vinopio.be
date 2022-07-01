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



namespace Mirasvit\QuickNavigation\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\QuickNavigation\Api\Data\SequenceInterface;
use Mirasvit\QuickNavigation\Model\ResourceModel\Sequence\CollectionFactory;
use Mirasvit\QuickNavigation\Model\SequenceFactory;

class SequenceRepository
{
    private $factory;

    private $collectionFactory;

    private $entityManager;

    public function __construct(
        SequenceFactory $factory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->factory           = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager     = $entityManager;
    }

    /**
     * @return SequenceInterface[]|\Mirasvit\QuickNavigation\Model\ResourceModel\Sequence\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return SequenceInterface
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @param int $id
     *
     * @return null|SequenceInterface
     */
    public function get($id)
    {
        $model = $this->create();

        $this->entityManager->load($model, $id);

        return $model->getId() ? $model : null;
    }

    /**
     * @param SequenceInterface $model
     *
     * @return SequenceInterface
     */
    public function save(SequenceInterface $model)
    {
        return $this->entityManager->save($model);
    }

    public function delete(SequenceInterface $model)
    {
        return $this->entityManager->delete($model);
    }
}
