<?php
namespace ColorOption\GroupCollection\Plugin;

use Magento\Quote\Model\Quote\Item\ToOrderItem as QuoteToOrderItem;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\SerializerInterface;

class ToOrderItem
{
	
	 protected $layout;
    protected $storeManager;
    protected $request;
    private $serializer;
 
    public function __construct(
        StoreManagerInterface $storeManager,
        LayoutInterface $layout,
        RequestInterface $request,
        SerializerInterface $serializer
    )
    {
        $this->layout = $layout;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->serializer = $serializer;
    }
    /**
     * aroundConvert
     *
     * @param QuoteToOrderItem $subject
     * @param \Closure $proceed
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param array $data
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function aroundConvert(
        QuoteToOrderItem $subject,
        \Closure $proceed,
        $item,
        $data = []
    ) {
        // Get Order Item
        $orderItem = $proceed($item, $data);
        // Get Quote Item's additional Options

        // Check if there is any additional options in Quote Item
		$additionalOptions = $item->getOptionByCode('additional_options');        
        // Check if there is any additional options in Quote Item                    
       if(!is_null($additionalOptions)) {
            // Get Order Item's other options
            $options = $orderItem->getProductOptions();
            // Set additional options to Order Item
            $options['additional_options'] = $this->serializer->unserialize($additionalOptions->getValue());
            $orderItem->setProductOptions($options);
        }

        return $orderItem;
    }
}