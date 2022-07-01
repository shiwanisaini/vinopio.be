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



namespace Mirasvit\Brand\Model;

use Magento\Framework\DataObject;
use Mirasvit\Brand\Api\Data\BrandInterface;
use Mirasvit\Brand\Repository\BrandPageRepository;
use Mirasvit\Brand\Service\BrandUrlService;
use Mirasvit\Brand\Service\ImageUrlService;

class Brand extends DataObject implements BrandInterface
{

    private $brandUrlService;


    private $imageUrlService;

    private $brandPageRepository;

    public function __construct(
        BrandPageRepository $brandPageRepository,
        ImageUrlService $imageUrlService,
        BrandUrlService $brandUrlService,
        array $data = []
    ) {
        $this->brandPageRepository = $brandPageRepository;
        $this->imageUrlService     = $imageUrlService;
        $this->brandUrlService     = $brandUrlService;

        parent::__construct($data);
    }


    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getAttributeId()
    {
        return $this->getData(self::ATTRIBUTE_ID);
    }

    public function getAttributeCode()
    {
        return $this->getData(self::ATTRIBUTE_CODE);
    }

    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        return $this->brandUrlService->getBrandUrl($this->getUrlKey(), $this->getLabel());
    }

    /**
     * @inheritdoc
     */
    public function getUrlKey()
    {
        return $this->getPage()->getId() ? $this->getPage()->getUrlKey() : null;
    }

    /**
     * @inheritdoc
     */
    public function getImage()
    {
        return $this->getPage()->getId() && $this->getPage()->getLogo()
            ? $this->imageUrlService->getImageUrl($this->getPage()->getLogo())
            : '';
    }

    /**
     * @inheritdoc
     */
    public function getPage()
    {
        if (!$this->getData(self::PAGE)) {
            $page = $this->brandPageRepository->getByOptionId($this->getId(), $this->getAttributeCode());
            $this->setData(self::PAGE, $page);
        }

        return $this->getData(self::PAGE);
    }
}
