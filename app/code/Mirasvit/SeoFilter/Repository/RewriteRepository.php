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
 * @package   mirasvit/module-seo-filter
 * @version   1.0.29
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoFilter\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;
use Mirasvit\SeoFilter\Api\Data\RewriteInterfaceFactory;
use Mirasvit\SeoFilter\Model\ResourceModel\Rewrite\CollectionFactory;
use Magento\Framework\DataObject;

class RewriteRepository
{
    private $factory;

    private $collectionFactory;

    private $entityManager;

    public function __construct(
        RewriteInterfaceFactory $factory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->factory           = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager     = $entityManager;
    }


    /**
     * @return RewriteInterface
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @return RewriteInterface[]|\Mirasvit\SeoFilter\Model\ResourceModel\Rewrite\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @param int $id
     *
     * @return RewriteInterface|DataObject|false
     */
    public function get($id)
    {
        $model = $this->create();

        $this->entityManager->load($model, $id);

        return $model->getId() ? $model : false;
    }

    /**
     * @param RewriteInterface $model
     *
     * @return RewriteInterface
     */
    public function save(RewriteInterface $model)
    {
        return $this->entityManager->save($model);
    }

    /**
     * @param RewriteInterface $model
     *
     * @return bool
     */
    public function delete(RewriteInterface $model)
    {
        return $this->entityManager->delete($model);
    }
}
