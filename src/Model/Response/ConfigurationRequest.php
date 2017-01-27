<?php

    namespace RatePAY\Model\Response;

    class ConfigurationRequest extends AbstractResponse
    {

        /**
         * Validates response
         */
        public function validateResponse()
        {
            if ($this->getStatusCode() == "OK" && $this->getResultCode() == 500) {
                $this->setResult(['interestrateMin' => (float) $this->getResponse()->content->{'installment-configuration-result'}->{'interestrate-min'}]);
                $this->setResult(['interestrateDefault' => (float) $this->getResponse()->content->{'installment-configuration-result'}->{'interestrate-default'}]);
                $this->setResult(['interestrateMax' => (float) $this->getResponse()->content->{'installment-configuration-result'}->{'interestrate-max'}]);
                $this->setResult(['interestRateMerchantTowardsBank' => (float) $this->getResponse()->content->{'installment-configuration-result'}->{'interest-rate-merchant-towards-bank'}]);
                $this->setResult(['monthNumberMin' => (int) $this->getResponse()->content->{'installment-configuration-result'}->{'month-number-min'}]);
                $this->setResult(['monthNumberMax' => (int) $this->getResponse()->content->{'installment-configuration-result'}->{'month-number-max'}]);
                $this->setResult(['monthLongrun' => (int) $this->getResponse()->content->{'installment-configuration-result'}->{'month-longrun'}]);
                $this->setResult(['amountMinLongrun' => (float) $this->getResponse()->content->{'installment-configuration-result'}->{'amount-min-longrun'}]);
                $this->setResult(['monthAllowed' => array_map('intval', explode(',', (string) $this->getResponse()->content->{'installment-configuration-result'}->{'month-allowed'}))]);
                $this->setResult(['validPaymentFirstdays' => (int) $this->getResponse()->content->{'installment-configuration-result'}->{'valid-payment-firstdays'}]);
                $this->setResult(['paymentFirstday' => (int) $this->getResponse()->content->{'installment-configuration-result'}->{'payment-firstday'}]);
                $this->setResult(['paymentAmount' => (float) $this->getResponse()->content->{'installment-configuration-result'}->{'payment-amount'}]);
                $this->setResult(['paymentLastrate' => (float) $this->getResponse()->content->{'installment-configuration-result'}->{'payment-lastrate'}]);
                $this->setResult(['rateMinNormal' => (float) $this->getResponse()->content->{'installment-configuration-result'}->{'rate-min-normal'}]);
                $this->setResult(['rateMinLongrun' => (float) $this->getResponse()->content->{'installment-configuration-result'}->{'rate-min-longrun'}]);
                $this->setResult(['serviceCharge' => (float) $this->getResponse()->content->{'installment-configuration-result'}->{'service-charge'}]);
                $this->setResult(['minDifferenceDueday' => (int) $this->getResponse()->content->{'installment-configuration-result'}->{'min-difference-dueday'}]);
                $this->setSuccessful();
            }
        }

        /**
         * Returns allowed months
         *
         * @return array
         */
        public function getAllowedMonths()
        {
            return $this->result['monthAllowed'];
        }

        /**
         * Returns minimum rate
         *
         * @return array
         */
        public function getMinimumRate($amount = 0)
        {
            //return ($amount >= $this->result['amountMinLongrun']) ? $this->result['rateMinLongrun'] : $this->result['rateMinNormal'];
            return $this->result['rateMinNormal'];
        }

    }
