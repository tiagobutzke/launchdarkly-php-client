<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Provider\CustomerProvider;
use Foodpanda\ApiSdk\Serializer;

class CustomerService
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var CustomerProvider
     */
    protected $provider;

    /**
     * @param CustomerProvider $provider
     * @param Serializer $serializer
     */
    public function __construct(CustomerProvider $provider, Serializer $serializer)
    {
        $this->serializer = $serializer;
        $this->provider = $provider;
    }

    /**
     * @param array $customerParameters
     *
     * @return Customer
     */
    public function createCustomer(array $customerParameters)
    {
        $customer = $this->serializer->denormalizeCustomer($customerParameters);


        return $this->provider->createCustomer($customer);
    }
}
