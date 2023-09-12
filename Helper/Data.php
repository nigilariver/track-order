<?php
namespace Riverstone\TrackOrder\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected const TRACK_ORDER_URL = 'track_order_section/general/dynamic_field';

    protected const TRACK_ORDER_STATUS = 'track_order_section/general/enable';
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serialize;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Helper file to get admin configuration values and changing template files

     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Serialize\Serializer\Json $serialize
     */

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\Serializer\Json $serialize
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->serialize = $serialize;
        parent::__construct($context);
    }

    /**
     * Getting store id

     * @return int
     */
    public function getStoreid()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Checking module status

     * @return int
     */
    public function getModuleStatus()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $moduleStatus = $this->scopeConfig->getValue(self::TRACK_ORDER_STATUS, $storeScope);
        return $moduleStatus;
    }

    /**
     * Changing frontend template if the module is enabled

     * @return string
     */
    public function getFrontendTemplate()
    {
        if ($this->getModuleStatus()) {
            $template = 'Riverstone_TrackOrder::items.phtml';
        } else {
            $template = 'Magento_Shipping::items.phtml';
        }

        return $template;
    }

    /**
     * Changing Sales Order Email Shipment Track Template if the module is enabled

     * @return string
     */
    public function getSalesOrderEmailShipmentTrackTemplate()
    {
        if ($this->getModuleStatus()) {
            $template = 'Riverstone_TrackOrder::email/shipment/track.phtml';
        } else {
            $template = 'Magento_Sales::email/shipment/track.phtml';
        }

        return $template;
    }

    /**
     * Changing Shipment Tracking template if the module is enabled

     * @return string
     */
    public function getShipmentTrackingTemplate()
    {
        if ($this->getModuleStatus()) {
            $template = 'Riverstone_TrackOrder::order/tracking/view.phtml';
        } else {
            $template = 'Magento_Shipping::order/tracking/view.phtml';
        }

        return $template;
    }
    
    /**
     * Changing Shipping Tracking Popup template if the module is enabled

     * @return string
     */
    public function getShippingTrackingPopupTemplate()
    {
        if ($this->getModuleStatus()) {
            $template = 'Riverstone_TrackOrder::tracking/popup.phtml';
        } else {
            $template = 'Magento_Shipping::tracking/popup.phtml';
        }

        return $template;
    }

    /**
     * Getting the admin configuration value and unserializing

     * @return array
     */
    public function getTrackingAdminData()
    {
        $config = $this->scopeConfig->getValue(self::TRACK_ORDER_URL, ScopeInterface::SCOPE_STORE, $this->getStoreid());

        if ($config == '' || $config == null):
            return;
        endif;

        $unserializedata = $this->serialize->unserialize($config);

        $apiUrlArray = [];
        foreach ($unserializedata as $key => $row) {
            $apiUrlArray[] = ['code' => $row['shipping_method'][0] , 'url' => $row['api_url']];
        }

        return $apiUrlArray;
    }

    /**
     * Getting tracking url

     * @param string $carrierCode
     * @param int $trackingNumber
     * @return string
     */
    public function getTrackingUrl($carrierCode, $trackingNumber)
    {
        $trackApiData = $this->getTrackingAdminData();

        $trackUrl = '';

        foreach ($trackApiData as $trackData):
            if ($trackData['code'] == $carrierCode):
                $trackUrl = str_replace('{trackingnumber}', $trackingNumber, $trackData['url']);
            endif;
        endforeach;

        return $trackUrl;
    }
}
