<?php
namespace Riverstone\TrackOrder\Block\Tracking;

class Popup extends \Magento\Shipping\Block\Tracking\Popup
{
    /**
     * @var \Magento\Shipping\Model\Tracking\Result\StatusFactory
     */
    private $trackingResultFactory;

    /**
     * @var \Riverstone\TrackOrder\Helper\Data
     */
    private $dataHelper;

    /**
     * Popup constructor.
     *
     * @param \Riverstone\TrackOrder\Helper\Data                                   $dataHelper
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory         $trackingResultFactory
     * @param \Magento\Framework\View\Element\Template\Context              $context
     * @param \Magento\Framework\Registry                                   $registry
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter
     * @param array                                                         $data
     */
    public function __construct(
        \Riverstone\TrackOrder\Helper\Data $dataHelper,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackingResultFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter,
        array $data = []
    ) {
        $this->trackingResultFactory = $trackingResultFactory;
        $this->dataHelper = $dataHelper;

        parent::__construct($context, $registry, $dateTimeFormatter, $data);
    }

    /**
     * Retrieve array of tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        $results = parent::getTrackingInfo();
        
        if (!$this->dataHelper->getModuleStatus()) {
            return $results;
        }

        foreach ($results as $shipping => $result) {
            foreach ($result as $key => $track) {
                if (!is_object($track)) {
                    continue;
                }

                $carrier = $track->getCarrier();
                $number = is_object($track) ? $track->getTracking() : $track['number'];
                
                $trackUrl = $this->dataHelper->getTrackingUrl($carrier, $number);
                
                if ($trackUrl) {
                    $url = $trackUrl;
                    $results[$shipping][$key] = $this->trackingResultFactory->create()->setData($track->getAllData())
                        ->setErrorMessage(null)
                        ->setUrl($url);
                }
            }
        }
        
        return $results;
    }
}
