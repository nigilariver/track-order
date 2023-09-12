<?php
namespace Riverstone\TrackOrder\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;

/**
 * Admin Configuration Dynamic form field
 */
class DropdownColumn extends Select
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shipconfig;

    /**
     * Admin Configuration Dynamic form field

     * @param Context $context
     * @param \Magento\Shipping\Model\Config $shipconfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */

    public function __construct(
        Context $context,
        \Magento\Shipping\Model\Config $shipconfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->shipconfig = $shipconfig;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritDoc
     */
    public function setInputName($value)
    {
        return $this->setName($value.'[]');
    }

    /**
     * @inheritDoc
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML

     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getShippingMethods());
        }

        return parent::_toHtml();
    }

    /**
     * Render Yes/No dropdown options

     * @return array
     */
    private function getSourceOptions()
    {
        return [
            ['label' => 'Yes','value' => '1'],
            ['label' => 'No','value' => '0'],
        ];
    }

    /**
     * Render active shipping method dropdown options

     * @return array
     */
    public function getShippingMethods()
    {
        $activeCarriers = $this->shipconfig->getActiveCarriers();
        $methods =[];

        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            $options = [];

            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    $code = $carrierCode . '_' . $methodCode;
                    $options[] = ['value' => $code, 'label' => $method];
                }
                $carrierTitle = $this->scopeConfig->getValue('carriers/'.$carrierCode.'/title');
            }

            $methods[] = ['value' => $carrierCode, 'label' => $carrierTitle];
        }

        return $methods;
    }
}
