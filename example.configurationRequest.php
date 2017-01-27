<?php

require __DIR__ . '/vendor/autoload.php';

require_once "ratepay_credentials.php";

/**********************************************************
 * The ConfigurationRequest the installment configuration *
 **********************************************************/

// ConfigurationRequest needs 'head' only
$mbHead = new RatePAY\ModelBuilder();
$mbHead->setArray([
    'SystemId' => "Example",
    'Credential' => [
        'ProfileId' => PROFILE_ID,
        'Securitycode' => SECURITYCODE
    ]
]);

$rb = new RatePAY\RequestBuilder(true); // Sandbox mode = true

$configurationRequest = $rb->callConfigurationRequest($mbHead);

if (!$configurationRequest->isSuccessful()) die("ConfigurationRequest not successful");

// The ConfigurationRequest response object provides following methods:
// getAllowedMonths(); // Returns list of allowed months {array}
// getMinimumRate(); // Returns minimum rate {float}

var_dump($configurationRequest->getAllowedMonths());
var_dump($configurationRequest->getMinimumRate());


/*********************************************************************************************************************************
 * The library throws decidedly exceptions. It's recommended to surround model building and request calls with try-catch-blocks. *
 *********************************************************************************************************************************/