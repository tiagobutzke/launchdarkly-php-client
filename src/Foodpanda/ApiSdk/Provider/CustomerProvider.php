<?php

namespace Foodpanda\ApiSdk\Provider;

use CommerceGuys\Guzzle\Oauth2\AccessToken;
use Foodpanda\ApiSdk\Api\CustomerApiClient;
use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Entity\Order\GuestCustomer;

class CustomerProvider extends AbstractProvider
{
    /**
     * @var CustomerApiClient
     */
    protected $client;

    /**
     * @param AccessToken $token
     *
     * @return Customer
     */
    public function getCustomer(AccessToken $token)
    {
        return $this->serializer->denormalizeCustomer($this->client->getCustomers($token, true));
    }

    /**
     * @param AccessToken $token
     *
     * @return array
     */
    public function getCustomerWithoutAddresses(AccessToken $token)
    {
        $customer = $this->client->getCustomers($token, false);

        return $customer;
    }

    /**
     * @param GuestCustomer $guestCustomer
     *
     * @return GuestCustomer
     */
    public function create(GuestCustomer $guestCustomer)
    {
        $guestCustomer1 = $this->serializer->serialize($guestCustomer, 'json');

        return $this->serializer->denormalizeGuestCustomer($this->client->createGuestCustomer($guestCustomer1, 1));
    }
}
