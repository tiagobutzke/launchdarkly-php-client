<?php

namespace Foodpanda\ApiSdk\Entity\Customer;

use Foodpanda\ApiSdk\Entity\Address\AddressesCollection;
use Foodpanda\ApiSdk\Entity\DataObject;

class Customer extends DataObject
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $first_name;

    /**
     * @var string
     */
    protected $last_name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $mobile_number;

    /**
     * @var string
     */
    protected $mobile_country_code;

    /**
     * @var string
     */
    protected $reference_code;

    /**
     * @var boolean
     */
    protected $has_password;

    /**
     * @var AddressesCollection
     */
    protected $customer_addresses;

    public function __construct()
    {
        $this->customer_addresses = new AddressesCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getMobileNumber()
    {
        return $this->mobile_number;
    }

    /**
     * @return string
     */
    public function getMobileCountryCode()
    {
        return $this->mobile_country_code;
    }

    /**
     * @return AddressesCollection
     */
    public function getCustomerAddresses()
    {
        return $this->customer_addresses;
    }

    /**
     * @return string
     */
    public function getReferenceCode()
    {
        return $this->reference_code;
    }

    /**
     * @return boolean
     */
    public function hasPassword()
    {
        return $this->has_password;
    }
}
