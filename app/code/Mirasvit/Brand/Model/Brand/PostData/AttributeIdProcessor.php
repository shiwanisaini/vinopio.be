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



namespace Mirasvit\Brand\Model\Brand\PostData;

use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Mirasvit\Brand\Api\Data\BrandPageInterface;
use Mirasvit\Brand\Api\Data\PostData\ProcessorInterface;
use Mirasvit\Brand\Model\Config\Config;

class AttributeIdProcessor implements ProcessorInterface
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    public function __construct(
        Config $config,
        ProductAttributeRepositoryInterface $productAttributeRepository
    ) {
        $this->config                     = $config;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function preparePostData($data)
    {
        if (!isset($data[BrandPageInterface::ID])) {
            $brandAttribute                         = $this->productAttributeRepository->get(
                $this->config->getGeneralConfig()->getBrandAttribute()
            );
            $data[BrandPageInterface::ATTRIBUTE_ID] = $brandAttribute->getAttributeId();
        }

        return $data;
    }
}
