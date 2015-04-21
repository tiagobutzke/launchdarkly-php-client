<?php

namespace Volo\EntityBundle\Entity\Order;

use Volo\EntityBundle\Entity\Address\Address;
use Volo\EntityBundle\Entity\Customer\Customer;
use Volo\EntityBundle\Entity\DataObject;

class PostGuestOrderResponse extends DataObject
{
    protected $objectClasses = [
        'customer' => Customer::class,
        'customer_address' => Address::class,
    ];

    /**
     * @var Customer
     */
    protected $customer;

    /**
     * @var Address
     */
    protected $customer_address;

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
