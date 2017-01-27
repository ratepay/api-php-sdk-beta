<?php

namespace RatePAY\Model\Request\SubModel;

class Constants {

    /*
     *  All available RatePAY payment methods
     */
    const RATEPAY_PAYMENT_METHODS = [
        "INVOICE",
        "INSTALLMENT",
        "ELV",
        "PREPAYMENT"
    ];

    /**
     * API SDK version
     */
    const API_SDK_SYSTEM_NAME = "RatePAY API PHP SDK";

    /**
     * API SDK version
     */
    const API_SDK_VERSION = "0.9.0";

    /**
     * Supported RatePAY API version
     */
    const RATEPAY_API_VERSION = "1.8";

    /**
     * CustomerAllowCreditInquiry
     */
    const CUSTOMER_ALLOW_CREDIT_INQUIRY = "yes";
}