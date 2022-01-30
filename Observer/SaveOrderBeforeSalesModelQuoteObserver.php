<?php

namespace Magelearn\ProductDeliveryDate\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magelearn\ProductDeliveryDate\Model\Validator;

class SaveOrderBeforeSalesModelQuoteObserver implements ObserverInterface
{
	/**
     * @var \Magento\Framework\DataObject\Copy
     */
    protected $objectCopyService;
	
    /**
     * @var Validator
     */
    private $validator;
    
	/**
     * SaveOrderBeforeSalesModelQuoteObserver constructor.
	 * @param \Magento\Framework\DataObject\Copy $objectCopyService
	 * @param Validator $validator
     */
    public function __construct(
        \Magento\Framework\DataObject\Copy $objectCopyService,
        Validator $validator
    ) {
		$this->objectCopyService = $objectCopyService;
		$this->validator = $validator;
    }
    /**
     *
     * @param  EventObserver $observer
     * @return $this
	 * @throws \Exception
     */
    public function execute(EventObserver $observer)
    {
        /* @var Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        /* @var Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');
        
        if (!$this->validator->validate($quote->getMagelearnDeliveryDate())) {
            throw new \Exception(__('Invalid Delevery Date'));
        }
        
		$order->setMagelearnDeliveryDate($quote->getMagelearnDeliveryDate());
		$this->objectCopyService->copyFieldsetToTarget('sales_convert_quote', 'to_order', $quote, $order);
		
        return $this;
    }
}
