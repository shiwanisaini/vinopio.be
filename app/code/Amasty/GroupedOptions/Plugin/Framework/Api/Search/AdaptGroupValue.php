<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Grouped Options for Magento 2
*/

declare(strict_types=1);

namespace Amasty\GroupedOptions\Plugin\Framework\Api\Search;

use Amasty\GroupedOptions\Model\GroupAttr\GetFakeKeyByCode;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class AdaptGroupValue
{
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var GetFakeKeyByCode
     */
    private $getFakeKeyByCode;

    public function __construct(
        GetFakeKeyByCode $getFakeKeyByCode,
        AttributeRepositoryInterface $attributeRepository,
        RequestInterface $request
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->request = $request;
        $this->getFakeKeyByCode = $getFakeKeyByCode;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSearch(SearchInterface $subject, SearchCriteriaInterface $searchCriteria): array
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $attributeValues = $filter->getValue();

                if (!is_array($attributeValues)) {
                    $attributeValues = [$attributeValues];
                }

                foreach ($attributeValues as $key => $attributeValue) {
                    try {
                        $attribute = $this->attributeRepository->get(
                            Product::ENTITY,
                            $filter->getField()
                        );
                    } catch (NoSuchEntityException $e) {
                        continue;
                    }

                    if ($attribute->getBackendType() === 'int') {
                        $attributeValue = $this->request->getParam(
                            $filter->getField()
                        ); // try load from request; magento convert to int
                    }

                    if (is_string($attributeValue)) {
                        $attributeValue = $this->getFakeKeyByCode->execute(
                            (int) $attribute->getAttributeId(),
                            $attributeValue
                        );

                        if ($attributeValue) {
                            $attributeValues[$key] = $attributeValue;
                            $filter->setValue($attributeValues);
                        }
                    }
                }
            }
        }

        return [$searchCriteria];
    }
}
