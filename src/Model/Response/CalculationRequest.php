<?php

    namespace RatePAY\Model\Response;

    use RatePAY\Service\LanguageService as lang;

    class CalculationRequest extends AbstractResponse
    {

        /**
         * Success codes
         *
         * @var array
         */
        private $successCodes = [603, 671, 688, 689, 695, 696, 697, 698, 699];

        private $responseTexts = [
            'DE' => [
                603 => "Die Wunschrate entspricht den vorgegebenen Bedingungen.",
                671 => "Die letzte Rate war niedriger als erlaubt. Laufzeit und/oder Rate wurden angepasst.",
                688 => "Die Rate war niedriger als f&uuml;r Ratenpl&auml;ne mit langer Laufzeit erlaubt. Die Laufzeit wurde angepasst.",
                689 => "Die Rate war niedriger als f&uuml;r Ratenpl&auml;ne mit kurzer Laufzeit erlaubt. Die Laufzeit wurde angepasst.",
                695 => "Die Rate ist zu hoch f&uuml;r die minimal verf&uuml;gbare Laufzeit. Die Rate wurde verringert.",
                696 => "Die Wunschrate ist zu niedrig. Die Rate wurde erh&ouml;ht.",
                697 => "F&uuml;r die gew&auml;hlte Ratenh&ouml;he ist keine entsprechende Laufzeit verf&uuml;gbar. Die Ratenh&ouml;he wurde angepasst.",
                698 => "Die Rate war zu niedrig f&uuml;r die maximal verf&uuml;gbare Laufzeit. Die Rate wurde erh&ouml;ht.",
                699 => "Die Rate ist zu hoch f&uuml;r die minimal verf&uuml;gbare Laufzeit. Die Rate wurde verringert.",
                ],
            /*'EN' => [

            ]*/
        ];
        

        /**
         * Validates response
         */
        public function validateResponse()
        {
            if ($this->getStatusCode() == "OK" && $this->getResultCode() == 502 && in_array($this->getReasonCode(), $this->getSuccessCodes())) {
                $this->setResult(['totalAmount' => (float) $this->getResponse()->content->{'installment-calculation-result'}->{'total-amount'}]);
                $this->setResult(['amount' => (float) $this->getResponse()->content->{'installment-calculation-result'}->{'amount'}]);
                $this->setResult(['interestRate' => (float) $this->getResponse()->content->{'installment-calculation-result'}->{'interest-rate'}]);
                $this->setResult(['interestAmount' => (float) $this->getResponse()->content->{'installment-calculation-result'}->{'interest-amount'}]);
                $this->setResult(['serviceCharge' => (float) $this->getResponse()->content->{'installment-calculation-result'}->{'service-charge'}]);
                $this->setResult(['annualPercentageRate' => (float) $this->getResponse()->content->{'installment-calculation-result'}->{'annual-percentage-rate'}]);
                $this->setResult(['monthlyDebitInterest' => (float) $this->getResponse()->content->{'installment-calculation-result'}->{'monthly-debit-interest'}]);
                $this->setResult(['numberOfRatesFull' => (int) $this->getResponse()->content->{'installment-calculation-result'}->{'number-of-rates'}]);
                $this->setResult(['numberOfRates' => (int) $this->getResponse()->content->{'installment-calculation-result'}->{'number-of-rates'} - 1]);
                $this->setResult(['rate' => (float) $this->getResponse()->content->{'installment-calculation-result'}->{'rate'}]);
                $this->setResult(['lastRate' => (float) $this->getResponse()->content->{'installment-calculation-result'}->{'last-rate'}]);
                $this->setResult(['paymentFirstday' => (int) $this->getResponse()->content->{'installment-calculation-result'}->{'payment-firstday'}]);
                $this->setResult(['displayedResponse' => ['DE' => $this->responseTexts['DE'][$this->getReasonCode()]]]);
                $this->setSuccessful();
            }
        }

        /**
         * Returns all success codes
         *
         * @return array
         */
        private function getSuccessCodes()
        {
            return $this->successCodes;
        }

        /**
         * Returns amount value for payment section (Payment Request)
         *
         * @return float
         */
        public function getPaymentAmount()
        {
            return $this->result['totalAmount'];
        }

        /**
         * Returns number of rates for installment details (Payment Request -> Payment)
         *
         * @return int
         */
        public function getInstallmentNumber()
        {
            return $this->result['numberOfRatesFull'];
        }

        /**
         * Returns rate for installment details (Payment Request -> Payment)
         *
         * @return float
         */
        public function getInstallmentAmount()
        {
            return $this->result['rate'];
        }

        /**
         * Returns last rate for installment details (Payment Request -> Payment)
         *
         * @return float
         */
        public function getLastInstallmentAmount()
        {
            return $this->result['lastRate'];
        }

        /**
         * Returns interest rate for installment details (Payment Request -> Payment)
         *
         * @return float
         */
        public function getInterestRate()
        {
            return $this->result['interestRate'];
        }

        /**
         * Returns payment firstday for installment details (Payment Request -> Payment)
         *
         * @return int
         */
        public function getPaymentFirstday()
        {
            return $this->result['paymentFirstday'];
        }

    }
