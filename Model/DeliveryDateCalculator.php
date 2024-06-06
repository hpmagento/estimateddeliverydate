<?php

namespace Hardik\EstimatedDeliveryDate\Model;

use DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class DeliveryDateCalculator
{
    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Calculate the earliest delivery date for the given carrier
     *
     * @param $carrierCode
     * @return mixed
     */
    public function getEarliestDeliveryDate($carrierCode): mixed
    {
        // Retrieve configuration values for the carrier
        $deliveryDays = $this->scopeConfig->getValue('carriers/' . $carrierCode . '/delivery_days', ScopeInterface::SCOPE_STORE);
        $dispatchDays = $this->scopeConfig->getValue('carriers/' . $carrierCode . '/dispatch_days', ScopeInterface::SCOPE_STORE);
        $deliveryTime = $this->scopeConfig->getValue('carriers/' . $carrierCode . '/delivery_time', ScopeInterface::SCOPE_STORE);
        $dispatchCutoff = $this->scopeConfig->getValue('carriers/' . $carrierCode . '/cutoff_time', ScopeInterface::SCOPE_STORE);
        $dispatchCutoff = str_replace(',', ':', $dispatchCutoff);
        $deliveryExceptionDates = $this->scopeConfig->getValue('carriers/' . $carrierCode . '/delivery_exception_dates', ScopeInterface::SCOPE_STORE);
        $dispatchExceptionDates = $this->scopeConfig->getValue('carriers/' . $carrierCode . '/dispatch_exception_dates', ScopeInterface::SCOPE_STORE);


        // Convert configuration values to arrays
        $deliveryDays = explode(',', $deliveryDays);
        $dispatchDays = explode(',', $dispatchDays);
        $deliveryExceptionDates = $this->convertToDateArray($deliveryExceptionDates);
        $dispatchExceptionDates = $this->convertToDateArray($dispatchExceptionDates);

        // Calculate earliest dispatch date
        $earliestDispatchDate = $this->getNextValidDate($dispatchDays, $dispatchExceptionDates, $dispatchCutoff);

        // Calculate the earliest delivery date by adding the delivery time to the dispatch date
        $earliestDeliveryDate = $this->addBusinessDays($earliestDispatchDate, $deliveryTime, $deliveryDays, $deliveryExceptionDates);
        return $earliestDeliveryDate->format('Y-m-d');
    }

    /**
     * Convert a newline-separated string of dates to an array of DateTime objects
     *
     * @param $dateString
     * @return array|void
     */
    private function convertToDateArray($dateString)
    {
        $dateArray = [];
        if (!empty($dateString)) {
            $dates = explode("\n", $dateString);
            foreach ($dates as $date) {
                $dateArray[] = DateTime::createFromFormat('d/m/Y', trim($date));
            }
        }
        return $dateArray;
    }

    /**
     * Get the next valid dispatch date based on allowed days and exceptions
     *
     * @param $validDays
     * @param $exceptionDates
     * @param $cutoffTime
     * @return DateTime
     */
    private function getNextValidDate($validDays, $exceptionDates, $cutoffTime): DateTime
    {
        $currentDate = new DateTime();
        if ($currentDate->format('H:i:s') > $cutoffTime) {
            $currentDate->modify('+1 day');
        }

        while ($this->isExceptionDate($currentDate, $exceptionDates)) {
            $currentDate->modify('+1 day');
        }
        while (!in_array($currentDate->format('N'), $validDays)) {
            $currentDate->modify('+1 day');
        }

        return $currentDate;
    }

    /**
     * Add a given number of business days to a date, considering valid days and exceptions
     *
     * @param $date
     * @param $days
     * @param $validDays
     * @param $exceptionDates
     * @return mixed
     */
    private function addBusinessDays($date, $days, $validDays, $exceptionDates): mixed
    {
        $businessDaysAdded = 0;
        while ($businessDaysAdded < $days) {
            $date->modify('+1 day');
            if (in_array($date->format('N'), $validDays) && !$this->isExceptionDate($date, $exceptionDates)) {
                $businessDaysAdded++;
            }
        }
        return $date;
    }

    /**
     * Check if a given date is in the exception dates
     *
     * @param $date
     * @param $exceptionDates
     * @return bool
     */
    private function isExceptionDate($date, $exceptionDates): bool
    {
        if (!empty($exceptionDates) && is_array($exceptionDates)) {
            foreach ($exceptionDates as $exceptionDate) {
                if ($date->format('Y-m-d') == $exceptionDate->format('Y-m-d')) {
                    return true;
                }
            }
        }
        return false;
    }
}
