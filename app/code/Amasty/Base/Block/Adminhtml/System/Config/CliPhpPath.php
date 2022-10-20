<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Magento 2 Base Package
*/
declare(strict_types=1);

namespace Amasty\Base\Block\Adminhtml\System\Config;

use Amasty\Base\Model\CliPhpResolver;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\UrlInterface;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;

class CliPhpPath extends Field
{
    /**
     * @var CliPhpResolver
     */
    private $cliPhpResolver;

    public function __construct(
        Context $context,
        CliPhpResolver $cliPhpResolver
    ) {
        parent::__construct($context);
        $this->cliPhpResolver = $cliPhpResolver;
    }

    public function render(AbstractElement $element)
    {
        try {
            $phpPath = $this->cliPhpResolver->getExecutablePath();
        } catch (\Exception $e) {
            $phpPath = '';
        }
        $element->setText($phpPath);

        return parent::render($element);
    }
}
