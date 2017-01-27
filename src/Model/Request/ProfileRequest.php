<?php

namespace RatePAY\Model\Request;

class ProfileRequest extends AbstractRequest
{

    /**
     * Request rule set
     *
     * @return bool
     */
    public function rule()
    {
        if (key_exists('value', $this->getHead()->admittedFields['TransactionId'])) {
            $this->setErrorMsg("Profile Request admits no transaction id");
            return false;
        }

        return true;
    }

}
