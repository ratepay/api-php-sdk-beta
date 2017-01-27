<?php

namespace RatePAY\Model\Request\SubModel\Content\Customer\Addresses;

use RatePAY\Model\Request\SubModel\AbstractModel;

class Address extends AbstractModel
{

    /**
     * List of admitted fields.
     * Each field is public accessible by certain getter and setter.
     * E.g:
     * Set firstname value by using setFirstName(var). Get firstname by using getFirstName(). (Please consider the camel case)
     *
     * Settings:
     * mandatory            = field is mandatory (or optional)
     * mandatoryByRule      = field is mandatory if rule is passed
     * optionalByRule       = field will only returned if rule is passed
     * default              = default value if no different value is set
     * isAttribute          = field is xml attribute to parent object
     * isAttributeTo        = field is xml attribute to field (in value)
     * instanceOf           = value has to be an instance of class (in value)
     * cdata                = value will be wrapped in CDATA tag
     *
     * @var array
     */
    public $admittedFields = [
        'Type' => [
            'mandatory' => true,
            'isAttribute' => true,
            'uppercase' => true
        ],
        'Salutation' => [
            'mandatory' => false
        ],
        'FirstName' => [
            'optionalByRule' => true
        ],
        'LastName' => [
            'optionalByRule' => true
        ],
        'Company' => [
            'mandatoryByRule' => true
        ],
        'Street' => [
            'mandatory' => true
        ],
        'StreetAdditional' => [
            'mandatory' => false
        ],
        'StreetNumber' => [
            'mandatory' => false
        ],
        'ZipCode' => [
            'mandatory' => true
        ],
        'City' => [
            'mandatory' => true
        ],
        'CountryCode' => [
            'mandatory' => true,
            'uppercase' => true
        ],
    ];

    /**
     * Address rule : names are only mandatory in billing addresses, company is mandatory in registry addresses
     *
     * @return bool
     */
    protected function rule()
    {
        if (strtoupper($this->admittedFields['Type']['value']) == 'DELIVERY') {
            if (!key_exists('value', $this->admittedFields['FirstName']) || !key_exists('value', $this->admittedFields['LastName'])) {
                $this->setErrorMsg("Delivery address requires firstname and lastname");
                return false;
            }
        }
        if (strtoupper($this->admittedFields['Type']['value']) == 'REGISTRY') {
            if (!key_exists('value', $this->admittedFields['Company'])) {
                $this->setErrorMsg("Registry address requires company");
                return false;
            }
        }
        return true;
    }

    /**
     * Manipulates the parent method to change address type and country code to upper case
     *
     * @return array
     */
    public function toArray()
    {
        $this->admittedFields['Type']['value'] = strtoupper($this->admittedFields['Type']['value']);
        $this->admittedFields['CountryCode']['value'] = strtoupper($this->admittedFields['CountryCode']['value']);

        return parent::toArray();
    }
}
