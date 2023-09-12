<?php
namespace Riverstone\TrackOrder\Block\Adminhtml\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Serialize admin config values
 */
class ArraySerialized extends ConfigValue
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * Serialize admin config values constructor

     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource $resource
     * @param AbstractDb $resourceCollection
     * @param SerializerInterface $serializer
     * @param array $data
     */

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        SerializerInterface $serializer,
        array $data = []
    ) {
        $this->serializer = $serializer;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        unset($value['__empty']);

        // Check for duplicate values
        if ($this->hasDuplicates($value)) {
            throw new \Magento\Framework\Exception\ValidatorException(
                __('Duplicate values are not allowed.')
            );
        }

        $encodedValue = $this->serializer->serialize($value);
        $this->setValue($encodedValue);
    }

    /**
     * Check for duplicate values.
     *
     * @param array $value
     * @return bool
     */
    private function hasDuplicates(array $value)
    {
        $flattenedValues = [];
    
        // Flatten the multidimensional array
        array_walk_recursive($value, function($item) use (&$flattenedValues) {
            $flattenedValues[] = $item;
        });
        
        $uniqueValues = array_unique($flattenedValues);
        return count($uniqueValues) !== count($flattenedValues);
    }

    /**
     * @inheritDoc
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        if ($value) {
            $decodedValue = $this->serializer->unserialize($value);
            $this->setValue($decodedValue);
        }
    }
}
