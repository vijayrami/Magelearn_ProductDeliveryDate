<?php
namespace Magelearn\ProductDeliveryDate\Model\Rewrite;

use Magento\Framework\DataObject\Copy;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Address\Item as AddressItem;
use Magento\Sales\Api\Data\OrderItemInterfaceFactory as OrderItemFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Framework\Serialize\Serializer\Serialize;

class ToOrderItem extends \Magento\Quote\Model\Quote\Item\ToOrderItem
{
    /**
     * @var Copy
     */
    protected $objectCopyService;

    /**
     * @var OrderItemFactory
     */
    protected $orderItemFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @param OrderItemFactory                        $orderItemFactory
     * @param Copy                                    $objectCopyService
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        OrderItemFactory $orderItemFactory,
        Copy $objectCopyService,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Checkout\Model\Session $session,
        Serialize $serializer
    ) {
        $this->orderItemFactory = $orderItemFactory;
        $this->objectCopyService = $objectCopyService;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->session = $session;
        $this->serializer = $serializer;
    }

    public function convert($item, $data = [])
    {
        $options = $item->getProductOrderOptions();
        if (!$options) {
            $options = $item->getProduct()->getTypeInstance()->getOrderOptions($item->getProduct());
        }
        $orderItemData = $this->objectCopyService->getDataFromFieldset(
            'quote_convert_item',
            'to_order_item',
            $item
        );
		if ($item instanceof \Magento\Quote\Model\Quote\Address\Item) {
            $orderItemData = array_merge(
                $orderItemData,
                $this->objectCopyService->getDataFromFieldset(
                    'quote_convert_address_item',
                    'to_order_item',
                    $item
                )
            );
        }
        if (!$item->getNoDiscount()) {
            $data = array_merge(
                $data,
                $this->objectCopyService->getDataFromFieldset(
                    'quote_convert_item',
                    'to_order_item_discount',
                    $item
                )
            );
        }

        $orderItem = $this->orderItemFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $orderItem,
            array_merge($orderItemData, $data),
            \Magento\Sales\Api\Data\OrderItemInterface::class
        );
        if ($item->getOptionByCode('additional_options')) {
            try {
                $options['additional_options'] = $this->serializer->unserialize($item->getOptionByCode('additional_options')->getValue());
            } catch (\Exception $e) {
                $options['additional_options'] = json_decode($item->getOptionByCode('additional_options')->getValue(), true);
            }
        }

        $quote = $this->session->getQuote();
        $deliveryDate = $this->session->getMagelearnDeliveryDateText() ?
            $this->session->getMagelearnDeliveryDateText() :
            $quote->getMagelearnDeliveryDateText();

        if ($deliveryDate) {
            $options['additional_options'] = $this->setDeliveryDateIfNotExist($options['additional_options'] ?? [], $deliveryDate);
        }

        $orderItem->setProductOptions($options);
        if ($item->getParentItem()) {
            $orderItem->setQtyOrdered(
                $orderItemData[OrderItemInterface::QTY_ORDERED] * $item->getParentItem()->getQty()
            );
        }
        return $orderItem;
    }

    public function setDeliveryDateIfNotExist($options, $deliveryDate)
    {
        $exist = false;
        foreach ($options as $key=>$option) {
            if (isset($option['code']) && ($option['code']=='product_delivery_date' || $option['code']=='order_delivery_date')) {
                $exist = true;
            }
        }

        if (!$exist) {
            $delivery_line = ((!empty($deliveryDate)) ? $deliveryDate . "\n" : '');
            $options[] = [
                'label'       => __('Delivery Date'),
                'code'        => 'order_delivery_date',
                'value'       => $delivery_line,
                'print_value' => $delivery_line
            ];
        }
        return $options;
    }
}