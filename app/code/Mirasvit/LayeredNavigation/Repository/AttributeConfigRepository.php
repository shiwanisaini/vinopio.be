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



namespace Mirasvit\LayeredNavigation\Repository;

use Exception;
use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\LayeredNavigation\Api\Data\AttributeConfigInterface;
use Mirasvit\LayeredNavigation\Api\Data\AttributeConfigInterfaceFactory;
use Mirasvit\LayeredNavigation\Model\ResourceModel\AttributeConfig\Collection;
use Mirasvit\LayeredNavigation\Model\ResourceModel\AttributeConfig\CollectionFactory;

class AttributeConfigRepository
{
    private $factory;

    private $collectionFactory;

    private $entityManager;

    public function __construct(
        AttributeConfigInterfaceFactory $factory,
        CollectionFactory $collectionFactory,
        EntityManager $entityManager
    ) {
        $this->factory           = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager     = $entityManager;
    }

    /**
     * @return AttributeConfigInterface
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @return Collection|AttributeConfigInterface[]
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @param int $id
     *
     * @return bool|AttributeConfigInterface
     */
    public function get($id)
    {
        $model = $this->create();

        $this->entityManager->load($model, $id);

        return $model->getId() ? $model : false;
    }

    /**
     * @param string $code
     *
     * @return bool|AttributeConfigInterface
     */
    public function getByAttributeCode($code)
    {
        /** @var AttributeConfigInterface $model */
        $model = $this->getCollection()
            ->addFieldToFilter(AttributeConfigInterface::ATTRIBUTE_CODE, $code)
            ->getFirstItem();

        return $model->getId() ? $model : false;
    }

    /**
     * @param AttributeConfigInterface $model
     *
     * @return object
     * @throws Exception
     */
    public function save(AttributeConfigInterface $model)
    {
        return $this->entityManager->save($model);
    }

    /**
     * @param AttributeConfigInterface $model
     *
     * @return bool
     * @throws Exception
     */
    public function delete(AttributeConfigInterface $model)
    {
        return $this->entityManager->delete($model);
    }
}
