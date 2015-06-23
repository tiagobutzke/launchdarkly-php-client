<?php

namespace Volo\FrontendBundle\Tests\Service;

use Foodpanda\ApiSdk\Entity\Address\Address;
use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Entity\Order\GuestCustomer;
use Volo\FrontendBundle\Tests\VoloTestCase;

class OrderManagerTest extends VoloTestCase
{
    public function testCreateOrder()
    {
        static::bootKernel();
        $customerManager = static::$kernel->getContainer()->get('volo_frontend.service.customer_manager');

        $customer = $this->createCustomer();
        $address  = $this->createAddress();
        $guestCustomer = new GuestCustomer();
        $guestCustomer->setCustomer($customer);
        $guestCustomer->setCustomerAddress($address);

        $createdGuestCustomer = $customerManager->create($guestCustomer);
        $this->assertInstanceOf(GuestCustomer::class, $createdGuestCustomer);
        $this->assertInstanceOf(Address::class, $createdGuestCustomer->getCustomerAddress());
    }

    /**
     * @return Customer
     */
    protected function createCustomer()
    {
        $customer = new Customer();
        $customer->setFirstName('first name');
        $customer->setLastName('last name');
        $customer->setEmail('info@example.com');
        $customer->setMobileCountryCode('+250');
        $customer->setMobileNumber('722219121');

        return $customer;
    }

    /**
     * @return Address
     */
    protected function createAddress()
    {
        $address = new Address();
        $address->setAddressLine1('foo');
        $address->setAreaId(127);
        $address->setAddressLine1('address 1');
        $address->setPostcode('12345');
        $address->setCityId(5);
        $address->setDeliveryInstructions('');
        $address->setDistrict('23234');
        $address->setCompany('foodpanda');
        $address->setFormattedCustomerAddress('formatted Address 1');

        return $address;
    }
}
