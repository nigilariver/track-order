<?php
namespace Riverstone\TrackOrder\Block\Adminhtml;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Riverstone\TrackOrder\Block\Adminhtml\Form\Field\DropdownColumn;

class DynamicFieldData extends AbstractFieldArray
{
    /**
     * @var DropdownColumn
     */
    private $dropdownRenderer;

    /**
     * Prepare existing row data object

     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'shipping_method',
            [
                'label' => __('Shipping Method'),
                'renderer' => $this->getDropdownRenderer(),
            ]
        );

        $this->addColumn(
            'api_url',
            [
                'label' => __('API URL'),
                'class' => 'required-entry',
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare dropdown options

     * @param DataObject $row
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $options = [];
        $dropdownField = $row->getDropdownField();

        if ($dropdownField !== null) {
            $dropDownOptions = $this->getDropdownRenderer()->calcOptionHash($dropdownField);
            $options['option_' . $dropDownOptions] = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * Dropdown renderer

     * @return DropdownColumn
     * @throws LocalizedException
     */
    private function getDropdownRenderer()
    {
        if (!$this->dropdownRenderer) {
            $render = ['is_render_to_js_template' => true];
            $this->dropdownRenderer = $this->getLayout()->createBlock(DropdownColumn::class, '', ['data' => $render]);
        }
        return $this->dropdownRenderer;
    }
}
