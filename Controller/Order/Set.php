<?php
namespace Magelearn\ProductDeliveryDate\Controller\Order;

use Magento\Framework\App\Action\Context;

class Set extends \Magento\Framework\App\Action\Action
{
    public $jsFormatToPhp = [
        'dd' => 'd',
        'mm' => 'm',
        'yy' => 'Y'
    ];

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $viewHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Checkout\Model\Session $session,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        parent::__construct($context);
        $this->jsonResultFactory = $jsonResultFactory;
        $this->session = $session;
        $this->quoteRepository = $quoteRepository;
        $this->timezone = $timezone;
    }

    /**
     * Product view action
     *
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $data = ['success' => 0];
        try {
            if ($deliveryDate = $this->getRequest()->getParam('delivery_date')) {
                $quote = $this->session->getQuote();
                $this->session->setMagelearnDeliveryDateText($deliveryDate);
				
                $format = $this->getRequest()->getParam('format');
                foreach ($this->jsFormatToPhp as $js => $php) {
                    $format = str_replace($js, $php, $format);
                }
                $date = \DateTime::createFromFormat($format, $deliveryDate);
                $timeStamp = $this->timezone->date($date)->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

                $quote->setMagelearnDeliveryDate($timeStamp);

                $this->quoteRepository->save($quote);
                $data['data'] = $timeStamp;
                $data['success'] = 1;
            } else {
                $data['message'] = __('Missing required parameters');
            }
        } catch (\Exception $e) {
            $data['message'] = $e->getMessage();
        }
        $result = $this->jsonResultFactory->create();
        $result->setData($data);
        return $result;
    }
}
