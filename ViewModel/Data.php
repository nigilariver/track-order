<?php
namespace Riverstone\TrackOrder\ViewModel;

class Data implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Riverstone\TrackOrder\Helper\Data $helperData
     */
    protected $helperData;

    /**
     * Getting helper data values

     * @param \Riverstone\TrackOrder\Helper\Data $helperData
     */

    public function __construct(
        \Riverstone\TrackOrder\Helper\Data $helperData
    ) {
        $this->helperData= $helperData;
    }
    
    /**
     * Return all helper functions

     * @return object
     */
    public function getDataHelper()
    {
        return $this->helperData;
    }
}
