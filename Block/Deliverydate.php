<?php
namespace Magelearn\ProductDeliveryDate\Block;

class Deliverydate extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magelearn\ProductDeliveryDate\Helper\Data
     */
    protected $_helper;

    protected $_productloader;
	
	protected $serialize;
	
	/**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magelearn\ProductDeliveryDate\Helper\Data $helper,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_helper = $helper;
        $this->_productloader = $_productloader;
		$this->serialize = $serialize;
		$this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getStoreConfig($key)
    {
        return $this->_helper->getCurrentStoreConfigValue($key);
    }
    public function canShow()
    {
        if ($this->_helper->getCurrentStoreConfigValue('deliverydate_options/activation/enabled')) {
            if ($this->_helper->getCurrentStoreConfigValue('deliverydate_options/activation/productwise')) {
                $id = $this->getRequest()->getParam('id');
				$current_product = $this->_productloader->create()->load($id);
                return $current_product->getShowDeliveryDatepicker();
            } else {
                return true;
            }
        }
        return false;
    }
}
