<?php
namespace Magelearn\ProductDeliveryDate\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Framework\App\RequestInterface;

class AddDeliveryDateToProduct implements ObserverInterface
{
    protected $_helper;
    protected $_backendAuthSession;
	
    /**
	* @var RequestInterface
	*/

	protected $_request;
	protected $_logger;

    public function __construct(
        \Magelearn\ProductDeliveryDate\Helper\Data $helper,
        RequestInterface $request,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        Serialize $serializer,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_helper = $helper;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_request = $request;
        $this->serializer = $serializer;
		$this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if (!$this->_helper->getCurrentStoreConfigValue('deliverydate_options/activation/enabled')) {
                return;
            }

            //print_r($this->_request->getParams());die;
            //print_r($this->_request->getFullActionName());die;
            $controllerModule = strtolower($this->_request->getControllerModule());
            if ($controllerModule=='magento_checkout' && ($this->_request->getActionName()=='add' || $this->_request->getActionName()=='updateItemOptions')) {
                if (!$this->_request->getParam('delivery_date')) {
                    return $this;
                }
                $product = $observer->getProduct();
                // add to the additional options array
                $additionalOptions = [];
                if ($additionalOption = $product->getCustomOption('additional_options')) {
                    try {
                        $additionalOptions = (array) $this->serializer->unserialize($additionalOption->getValue());
                    } catch (\Exception $e) {
                        $additionalOptions = json_decode($additionalOption->getValue(), true);
                    }
                }
                $additionalOptions = $this->removeDuplicate($additionalOptions);
                $deliverydate = $this->formatDeliveryDetails($this->_request->getParam('delivery_date'));
                $additionalOptions = array_merge($additionalOptions, $deliverydate);

                $mergedAdditionalOptions = $this->serializer->serialize($additionalOptions);
                $result = json_decode($mergedAdditionalOptions, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $mergedAdditionalOptions = json_encode($additionalOptions);
                }
				//$this->_logger->debug($mergedAdditionalOptions);die;
				
                // add the additional options array with the option code additional_options
                $product->addCustomOption('additional_options', $mergedAdditionalOptions);

                return $this;
            }
        } catch (\Exception $e) {
        }

        return $this;
    }

    public function removeDuplicate($additionalOptions)
    {
        foreach ($additionalOptions as $key=>$option) {
            if (isset($option['code']) && $option['code']=='product_delivery_date') {
                unset($additionalOptions[$key]);
            }
        }
        return $additionalOptions;
    }

    /**
     * format delivery date text
     */
    public function formatDeliveryDetails($delivery_date)
    {
        $delivery_details= $delivery_date;
        /*$delivery_details = sprintf('%s  : %s',
            __('Product Delivery Date'),
            $delivery_date
        );
        */

        $delivery_line = ((!empty($delivery_details)) ? $delivery_details . "\n" : '');

        $delivery[0] = [
            'label'       => __('Product Delivery Date'),
            'code'        => 'product_delivery_date',
            'value'       => $delivery_line,
            'print_value' => $delivery_line
        ];

        return $delivery;
    }
}
