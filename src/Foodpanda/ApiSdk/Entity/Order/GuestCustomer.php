<?php

namespace Foodpanda\ApiSdk\Entity\Order;

use Foodpanda\ApiSdk\Entity\Address\Address;
use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Entity\DataObject;

class GuestCustomer extends DataObject
{
    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var Address
     */
    protected $customer_address;

    public function __construct()
    {
        $this->customer = new Customer();
        $this->customer_address = new Address();
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return Address
     */
    public function getCustomerAddress()
    {
        return $this->customer_address;
    }

    /**
     * @param Address $customer_address
     */
    public function setCustomerAddress($customer_address)
    {
        $this->customer_address = $customer_address;
    }
}
