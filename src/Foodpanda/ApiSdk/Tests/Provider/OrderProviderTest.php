<?php

namespace Foodpanda\ApiSdk\Tests\Provider;

use Foodpanda\ApiSdk\Api\CustomerApiClient;
use Foodpanda\ApiSdk\ApiFactory;
use Foodpanda\ApiSdk\Entity\Address\Address;
use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Entity\Order\GuestCustomer;
use Foodpanda\ApiSdk\Provider\CustomerProvider;
use Foodpanda\ApiSdk\Tests\ApiSdkTestSuite;

class OrderProviderTest extends ApiSdkTestSuite
{
    public function testCreateGuestOrder()
    {
        $customer = $this->createCustomer();

        $address = $this->createAddress();

        $newGuestCustomer = new GuestCustomer();
        $newGuestCustomer->setCustomer($customer);
        $newGuestCustomer->setCustomerAddress($address);

        $orderApiClient = new CustomerApiClient($this->getClient(), '', '');
        $serializer = ApiFactory::createSerializer();
        $provider = new CustomerProvider($orderApiClient, $serializer);

        $guestCustomer = $provider->create($newGuestCustomer);
        $this->assertInstanceOf(GuestCustomer::class, $guestCustomer);
        $this->assertInstanceOf(Address::class, $guestCustomer->getCustomerAddress());
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
