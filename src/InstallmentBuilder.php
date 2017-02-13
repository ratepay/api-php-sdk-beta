<?php

namespace RatePAY;

use RatePAY\Model\Request\ConfigurationRequest;
use RatePAY\ModelBuilder;
use RatePAY\RequestBuilder;
use RatePAY\Service\Util;
use RatePAY\Service\LanguageService;
use RatePAY\Exception\RequestException;

class InstallmentBuilder
{

    /**
     * Sandbox mode
     *
     * @var bool
     */
    private $sandbox = false;

    /**
     * RatePAY profile id
     *
     * @var string
     */
    private $profileId;

    /**
     * RatePAY security code
     *
     * @var string
     */
    private $securitycode;

    /**
     * Language object contains translation text blocks
     *
     * @var LanguageService
     */
    private $lang;


    public function __construct($sandbox = false, $profileId = null, $securitycode = null, $language = "DEU")
    {
        if ($sandbox) {
            $this->sandbox = true;
        }

        if (!is_null($profileId)) {
            $this->setProfileId($profileId);
        }

        if (!is_null($securitycode)) {
            $this->setSecuritycode($securitycode);
        }

        $this->lang = new LanguageService($language);
    }

    /**
     * Sets RatePAY profile id
     *
     * @param string $profileId
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;
    }

    /**
     * Sets RatePAY security code
     * @param string $securitycode
     */
    public function setSecuritycode($securitycode)
    {
        $this->securitycode = $securitycode;
    }

    /**
     * Sets current language
     *
     * @param $language
     */
    public function setLanguage($language)
    {
        $this->lang = new LanguageService($language);
    }

    /**
     * Calls Configuration Request
     *
     * @return ConfigurationRequest
     * @throws RequestException
     */
    private function getInstallmentConfiguration()
    {
        $rb = new RequestBuilder($this->sandbox);
        $configuration = $rb->callConfigurationRequest($this->getHead());

        if (!$configuration->isSuccessful()) {
            throw new RequestException("Configuration Request not successful - reason: '" . $configuration->getReasonMessage() . "'");
        }

        return $configuration;
    }

    /**
     * Returns processed html template
     *
     * @param float $amount
     * @param string $template
     * @return string
     */
    public function getInstallmentConfigByTemplate($amount, $template)
    {
        $configuration = $this->getInstallmentConfiguration();

        $replacements = array_merge(
            ['rp_minimumRate' => $configuration->getMinRate()],
            ['rp_maximumRate' => $configuration->getMaxRate($amount)],
            ['rp_amount'      => $amount],
            $this->lang->getArray()
        );

        $returnTemplate = Util::templateReplacer($template, $replacements);
        $returnTemplate = Util::templateLooper($returnTemplate, ['rp_allowedMonths' => $configuration->getAllowedMonths($amount)]);

        return $returnTemplate;
    }

    /**
     * Returns installment configuration as JSON
     *
     * @param $amount
     * @return string
     */
    public function getInstallmentConfigAsJson($amount)
    {
        $configuration = $this->getInstallmentConfiguration();

        return json_encode([
            'rp_minimumRate'   => $configuration->getMinRate(),
            'rp_maximumRate'   => $configuration->getMaxRate($amount),
            'rp_allowedMonths' => $configuration->getAllowedMonths($amount)
        ]);
    }


    /**
     * Calls Calculation Request
     *
     * @return CalculationRequest
     * @throws RequestException
     */
    private function getInstallmentCalculation($type, $value, $amount)
    {
        if (floatval($value) <= 0) {
            throw new RequestException("Invalid calculation value");
        }

        if (floatval($amount) <= 0) {
            throw new RequestException("Invalid calculation amount");
        }

        $installmentCalculation = [
            'InstallmentCalculation' => [
                'Amount' => $amount
            ]
        ];

        switch ($type) {
            case 'time':
                $installmentCalculation['InstallmentCalculation']['CalculationTime']['Month'] = $value;
                break;
            case 'rate':
                $installmentCalculation['InstallmentCalculation']['CalculationRate']['Rate'] = $value;
                break;
            default:
                throw new RequestException("Invalid calculation type. 'time' or 'rate' expected");
        }

        $mbContent = new ModelBuilder('Content');
        $mbContent->setArray($installmentCalculation);

        $rb = new RequestBuilder($this->sandbox);
        $calculation = $rb->callCalculationRequest($this->getHead(), $mbContent)->subtype('calculation-by-' . $type);
        // ToDo: Surround with Try-Catch-Block

        if (!$calculation->isSuccessful()) {
            throw new RequestException("Calculation Request not successful - reason: '" . $calculation->getReasonMessage() . "'");
        }

        return $calculation;
    }

    /**
     * Returns processed html template
     *
     * @param $amount
     * @param $type
     * @param $value
     * @param $template
     * @return string
     */
    public function getInstallmentPlanByTemplate($amount, $type, $value, $template)
    {
        $calculation = $this->getInstallmentCalculation($type, $value, $amount);

        $rpReasonCodeTranslation = 'rp_reason_code_translation_' . $calculation->getReasonCode();

        $replacements = array_merge(
            [
                'rp_amount'               => number_format($amount, 2, ",", "."),
                'rp_serviceCharge'        => $calculation->getServiceCharge(),
                'rp_annualPercentageRate' => $calculation->getAnnualPercentageRate(),
                'rp_monthlyDebitInterest' => $calculation->getMonthlyDebitInterest(),
                'rp_interestRate'         => $calculation->getInterestRate(),
                'rp_interestAmount'       => $calculation->getInterestAmount(),
                'rp_totalAmount'          => $calculation->getTotalAmount(),
                'rp_numberOfRatesFull'    => $calculation->getNumberOfRatesFull(),
                'rp_numberOfRates'        => $calculation->getNumberOfRates(),
                'rp_rate'                 => $calculation->getRate(),
                'rp_lastRate'             => $calculation->getLastRate(),
                'rp_responseText'         => $this->lang->$rpReasonCodeTranslation()
            ],
            $this->lang->getArray()
        );

        return Util::templateReplacer($template, $replacements);
    }

    /**
     * Returns installment calculation as JSON
     *
     * @param $amount
     * @param $type
     * @param $value
     * @return string
     */
    public function getInstallmentPlanAsJson($amount, $type, $value)
    {
        $configuration = $this->getInstallmentCalculation($type, $value, $amount);

        return json_encode($configuration);
    }

    /**
     * Returns commen head model
     *
     * @return \RatePAY\ModelBuilder
     */
    private function getHead()
    {
        $mbHead = new ModelBuilder();
        $mbHead->setArray([
            'SystemId' => "RatePAY API PHP SDK",
            'Credential' => [
                'ProfileId' => $this->profileId,
                'Securitycode' => $this->securitycode
            ]
        ]);

        return $mbHead;
    }

}