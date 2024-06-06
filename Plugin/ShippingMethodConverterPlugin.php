<?php

namespace Hardik\EstimatedDeliveryDate\Plugin;

use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;
use Hardik\EstimatedDeliveryDate\Model\DeliveryDateCalculator;

class ShippingMethodConverterPlugin
{
    /**
     * @var ShippingMethodExtensionFactory
     */
    protected ShippingMethodExtensionFactory $extensionFactory;
    /**
     * @var DeliveryDateCalculator
     */
    protected DeliveryDateCalculator $deliveryDateCalculator;

    /**
     * @param ShippingMethodExtensionFactory $extensionFactory
     * @param DeliveryDateCalculator $deliveryDateCalculator
     */
    public function __construct(
        ShippingMethodExtensionFactory $extensionFactory,
        DeliveryDateCalculator         $deliveryDateCalculator
    )
    {
        $this->extensionFactory = $extensionFactory;
        $this->deliveryDateCalculator = $deliveryDateCalculator;
    }

    /**
     * Plugin method to add the earliest delivery date to the shipping method data object
     *
     * @param $subject
     * @param $result
     * @param $rateModel
     * @return mixed
     */
    public function afterModelToDataObject($subject, $result, $rateModel): mixed
    {
        //add custom methods carrier code if necessary
        $allowShippingMethod = ['flatrate', 'freeshipping', 'tablerate'];
        $carrierCode = $result->getCarrierCode();
        $extensionAttribute = $result->getExtensionAttributes()
            ? $result->getExtensionAttributes() : $this->extensionFactory->create();
        $earliestDeliveryDate = '';
        if(in_array($carrierCode, $allowShippingMethod)) {
            // Calculate the earliest delivery date using the DeliveryDateCalculator
            $earliestDeliveryDate = $this->deliveryDateCalculator->getEarliestDeliveryDate($carrierCode);
        }
        // Set the earliest delivery date in the extension attributes
        $extensionAttribute->setEarliestDeliveryDate($earliestDeliveryDate);
        $result->setExtensionAttributes($extensionAttribute);
        return $result;
    }
}
