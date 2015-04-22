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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getMobileNumber()
    {
        return $this->mobile_number;
    }

    /**
     * @param string $mobile_number
     */
    public function setMobileNumber($mobile_number)
    {
        $this->mobile_number = $mobile_number;
    }

    /**
     * @return string
     */
    public function getMobileCountryCode()
    {
        return $this->mobile_country_code;
    }

    /**
     * @param string $mobile_country_code
     */
    public function setMobileCountryCode($mobile_country_code)
    {
        $this->mobile_country_code = $mobile_country_code;
    }

    /**
     * @return AddressesCollection
     */
    public function getCustomerAddresses()
    {
        return $this->customer_addresses;
    }

    /**
     * @param AddressesCollection $customer_addresses
     */
    public function setCustomerAddresses($customer_addresses)
    {
        $this->customer_addresses = $customer_addresses;
    }

    /**
     * @return string
     */
    public function getReferenceCode()
    {
        return $this->reference_code;
    }

    /**
     * @param string $reference_code
     */
    public function setReferenceCode($reference_code)
    {
        $this->reference_code = $reference_code;
    }

    /**
     * @return boolean
     */
    public function hasPassword()
    {
        return $this->has_password;
    }

    /**
     * @param boolean $has_password
     */
    public function setHasPassword($has_password)
    {
        $this->has_password = $has_password;
    }
}
