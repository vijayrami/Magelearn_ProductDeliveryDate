<?php

namespace Magelearn\ProductDeliveryDate\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\State as AppState;
use Magento\Sales\Model\AdminOrder\Create as AdminOrderCreate;

class ConfigProvider
{
	const XPATH_ACT_ENABLED		= 'deliverydate_options/activation/enabled';
    const XPATH_ACT_REQUIRED    = 'deliverydate_options/activation/required';
    const XPATH_ACT_DAYDISABLE  = 'deliverydate_options/activation/daydisable';
    const XPATH_ACT_DATEDISABLE = 'deliverydate_options/activation/datedisable';
    const XPATH_ACT_MINDATE 	= 'deliverydate_options/activation/mindate';
	const XPATH_ACT_MAXDATE 	= 'deliverydate_options/activation/maxdate';
	const XPATH_ACT_DATEFORMAT  = 'deliverydate_options/activation/dateformat';
    const XPATH_ACT_PRODUCTWISE = 'deliverydate_options/activation/productwise';
	
    const XPATH_ORDER_ENABLED     = 'deliverydate_options/order/enabled';
    const XPATH_ORDER_REQUIRED    = 'deliverydate_options/order/required';
    const XPATH_ORDER_DAYDISABLE  = 'deliverydate_options/order/daydisable';
	const XPATH_ORDER_DATEDISABLE = 'deliverydate_options/order/datedisable';
	const XPATH_ORDER_MINDATE     = 'deliverydate_options/order/mindate';
    const XPATH_ORDER_MAXDATE     = 'deliverydate_options/order/maxdate';
    const XPATH_ORDER_DATEFORMAT  = 'deliverydate_options/order/dateformat';
	
    /**
     * @var int
     */
    protected $storeId;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var AdminOrderCreate
     */
    protected $adminOrderCreate;
	
	protected $serialize;
	
    /**
     * Config constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param AppState $appState
     * @param AdminOrderCreate $adminOrderCreate
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        AppState $appState,
        AdminOrderCreate $adminOrderCreate,
        \Magento\Framework\Serialize\Serializer\Json $serialize
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->appState = $appState;
        $this->adminOrderCreate = $adminOrderCreate;
		$this->serialize = $serialize;
    }
	
	/**
     * @return mixed
     */
    public function getOrderEnabled()
    {
        $store = $this->getStoreId();

        return $this->scopeConfig->getValue(self::XPATH_ORDER_ENABLED, ScopeInterface::SCOPE_STORE, $store);
    }
    /**
     * @return mixed
     */
    public function getOrderRequired()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_ORDER_REQUIRED, ScopeInterface::SCOPE_STORE, $store);
    }
	/**
     * @return mixed
     */
    public function getOrderDaydisable()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_ORDER_DAYDISABLE, ScopeInterface::SCOPE_STORE, $store);
    }
	/**
     * @return mixed
     */
    public function getOrderDatedisable()
    {
        $store = $this->getStoreId();
        $black_out = $this->scopeConfig->getValue(self::XPATH_ORDER_DATEDISABLE, ScopeInterface::SCOPE_STORE, $store);
		if (empty($black_out)) return false;
         
        $black_out_data = $this->serialize->unserialize($black_out);
         
        $black_out_options  = array();
        foreach($black_out_data as  $condtion){
            $condtionName = strtolower(str_replace(" ","_",$condtion['date']));
            $black_out_options[] = array(
                'date' =>   $condtionName,
                'content' =>   $condtion['content'],                            
            );
        }
 
        return $black_out_options;
    }
	/**
     * @return mixed
     */
    public function getOrderMindate()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_ORDER_MINDATE, ScopeInterface::SCOPE_STORE, $store);
    }
	/**
     * @return mixed
     */
    public function getOrderMaxdate()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_ORDER_MAXDATE, ScopeInterface::SCOPE_STORE, $store);
    }
	/**
     * @return mixed
     */
    public function getOrderDateformat()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_ORDER_DATEFORMAT, ScopeInterface::SCOPE_STORE, $store);
    }
	
	/**
     * @return mixed
     */
    public function getActivationEnabled()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_ACT_ENABLED, ScopeInterface::SCOPE_STORE, $store);
    }
	/**
     * @return mixed
     */
    public function getActivationRequired()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_ACT_REQUIRED, ScopeInterface::SCOPE_STORE, $store);
    }
	/**
     * @return mixed
     */
    public function getActivationDaydisable()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_ACT_DAYDISABLE, ScopeInterface::SCOPE_STORE, $store);
    }
	/**
     * @return mixed
     */
    public function getActivationDatedisable()
    {
        $store = $this->getStoreId();
        $black_out = $this->scopeConfig->getValue(self::XPATH_ACT_DATEDISABLE, ScopeInterface::SCOPE_STORE, $store);
		if (empty($black_out)) return false;
         
        $black_out_data = $this->serialize->unserialize($black_out);
         
        $black_out_options  = array();
        foreach($black_out_data as  $condtion){
            $condtionName = strtolower(str_replace(" ","_",$condtion['date']));
            $black_out_options[] = array(
                'date' =>   $condtionName,
                'content' =>   $condtion['content'],                            
            );
        }
 
        return $black_out_options;
    }
	/**
     * @return mixed
     */
    public function getActivationMindate()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_ACT_MINDATE, ScopeInterface::SCOPE_STORE, $store);
    }
	/**
     * @return mixed
     */
    public function getActivationMaxdate()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_ACT_MAXDATE, ScopeInterface::SCOPE_STORE, $store);
    }
	/**
     * @return mixed
     */
    public function getActivationDateformat()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_ACT_DATEFORMAT, ScopeInterface::SCOPE_STORE, $store);
    }
	/**
     * @return mixed
     */
    public function getActivationProductwise()
    {
        $store = $this->getStoreId();
        return $this->scopeConfig->getValue(self::XPATH_ACT_PRODUCTWISE, ScopeInterface::SCOPE_STORE, $store);
    }
	/**
     * @return int
     */
    public function getStoreId()
    {
        if (!$this->storeId) {
            if ($this->appState->getAreaCode() == 'adminhtml') {
                $this->storeId = $this->adminOrderCreate->getQuote()->getStoreId();
            } else {
                $this->storeId = $this->storeManager->getStore()->getStoreId();
            }
        }

        return $this->storeId;
    }

    public function getConfig()
    {
    	$order_enabled = $this->getOrderEnabled();
    	$order_required = $this->getOrderRequired();
    	$order_daydisable = $this->getOrderDaydisable();
    	$order_datedisable = $this->getOrderDatedisable();
    	$order_mindate = $this->getOrderMindate();
    	$order_maxdate = $this->getOrderMaxdate();
    	$order_dateformat = $this->getOrderDateformat();
    	
		$order_noday = 0;
        if($order_daydisable == -1) {
            $order_noday = 1;
        }
		
		$activation_enabled = $this->getActivationEnabled();
		$activation_required = $this->getActivationRequired();
		$activation_daydisable = $this->getActivationDaydisable();
		$activation_datedisable = $this->getActivationDatedisable();
		$activation_mindate = $this->getActivationMindate();
		$activation_maxdate = $this->getActivationMaxdate();
		$activation_dateformat = $this->getActivationDateformat();
		$activation_productwise = $this->getActivationProductwise();
		
		$config = [
            'magelearnDeliveryDate' => [
                'order' => [
                    'enabled' => $order_enabled,
                    'required' => $order_required,
                    'daydisable' => $order_daydisable,
                    'datedisable' => $order_datedisable,
                    'mindate' => $order_mindate,
                    'maxdate' => $order_maxdate,
                    'dateformat' => $order_dateformat,
                    'noday' => $order_noday,
                ],
                'activation' => [
                	'enabled' => $activation_enabled,
                    'required' => $activation_required,
                    'daydisable' => $activation_daydisable,
                    'datedisable' => $activation_datedisable,
                    'mindate' => $activation_mindate,
                    'maxdate' => $activation_maxdate,
                    'dateformat' => $activation_dateformat,
                    'productwise' => $activation_productwise
                ]
            ]
        ];

        return $config;
    }
}
