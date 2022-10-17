<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Shop by Base for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\ShopbyBase\Model\Di;

use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Separate wrapper for view models because they must implement ArgumentInterface.
 */
class ViewModelWrapper extends Wrapper implements ArgumentInterface
{
}
