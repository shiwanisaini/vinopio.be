<?php

declare(strict_types=1);

namespace Amasty\SeoToolkitLite\Controller\Adminhtml\Redirect;

use Magento\Framework\Data\Collection\AbstractDb;

class MassDisable extends MassActionAbstract
{
    /**
     * @param AbstractDb $collection
     */
    public function doAction(AbstractDb $collection)
    {
        $collectionSize = $collection->getSize();
        foreach ($collection as $redirect) {
            $redirect->setStatus(0);
            $this->redirectResource->save($redirect);
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deactivated.', $collectionSize));
    }
}
