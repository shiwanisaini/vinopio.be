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



namespace Mirasvit\QuickNavigation\Plugin\Frontend;

use Mirasvit\QuickNavigation\Context;
use Mirasvit\QuickNavigation\Service\SequenceService;

/**
 * @see \Magento\Framework\Controller\ResultInterface::renderResult()
 */
class MemorizeSequencePlugin
{
    private $context;

    private $sequenceService;

    public function __construct(
        Context $context,
        SequenceService $sequenceService
    ) {
        $this->context         = $context;
        $this->sequenceService = $sequenceService;
    }

    /**
     * @param object $subject
     * @param object $result
     *
     * @return object
     */
    public function afterRenderResult($subject, $result)
    {
        $filters = $this->context->getState()->getFilters();
        if (count($filters) === 0) {
            return $result;
        }

        $sequence = $this->sequenceService->createSequence();

        $sequence = $this->sequenceService->ensureSequence($sequence);
        $this->sequenceService->increasePopularity($sequence);

        return $result;
    }
}
