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



namespace Mirasvit\LayeredNavigation\Plugin\Frontend\Catalog\Controller\Category\View;

use Mirasvit\LayeredNavigation\Model\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Service\AjaxResponseService;

/**
 * @see \Magento\Catalog\Controller\Category\View::execute()
 */
class AjaxCategoryPlugin
{
    use ConfigTrait;

    private $ajaxResponseService;

    public function __construct(
        AjaxResponseService $ajaxResponseService
    ) {
        $this->ajaxResponseService = $ajaxResponseService;
    }

    /**
     * @param \Magento\Catalog\Controller\Category\View $subject
     * @param \Magento\Framework\View\Result\Page       $page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function afterExecute($subject, $page)
    {
        if ($this->isAllowed($subject->getRequest())) {
            return $this->ajaxResponseService->getAjaxResponse($page);
        }

        return $page;
    }
}
