<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Entity\Order\GuestCustomer;
use Foodpanda\ApiSdk\Provider\CustomerProvider;
use Foodpanda\ApiSdk\Serializer;
use libphonenumber\PhoneNumber;
use Volo\FrontendBundle\Service\Exception\PhoneNumberValidationException;

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
     * @var PhoneNumberService
     */
    protected $phoneService;

    /**
     * @param CustomerProvider $provider
     * @param Serializer $serializer
     * @param PhoneNumberService $phoneService
     * @internal param PhoneNumberUtil $phoneNumberUtil
     */
    public function __construct(CustomerProvider $provider, Serializer $serializer, PhoneNumberService $phoneService)
    {
        $this->serializer = $serializer;
        $this->provider = $provider;
        $this->phoneService = $phoneService;
    }

    /**
     * @param array $customerParameters
     *
     * @throws PhoneNumberValidationException
     *
     * @return Customer
     */
    public function createCustomer(array $customerParameters)
    {
        $customer = $this->serializer->denormalizeCustomer($customerParameters);
        $validPhoneNumber = $this->validatePhoneNumber($customer->getMobileNumber());
        $this->setParsedMobileNumber($customer, $validPhoneNumber);

        return $this->provider->createCustomer($customer);
    }

    /**
     * @param Customer $customer
     * @param PhoneNumber $parsedNumber
     */
    protected function setParsedMobileNumber($customer, PhoneNumber $parsedNumber) {
        $customer->setMobileCountryCode($parsedNumber->getCountryCode());
        $customer->setMobileNumber($parsedNumber->getNationalNumber());
    }

    /**
     * @param string $mobileNumber
     *
     * @return PhoneNumber
     */
    protected function validatePhoneNumber($mobileNumber)
    {
        $parsedNumber = $this->phoneService->parsePhoneNumber($mobileNumber);
        $this->phoneService->validateMobilePhone($parsedNumber);

        return $parsedNumber;
    }

    /**
     * @param array $customer
     * @param array $address
     *
     * @return GuestCustomer
     */
    public function createGuestCustomer(array $customer, array $address)
    {
        $guestCustomerData = [
            'customer_address' => $address,
            'customer'         => $customer
        ];

        $guestCustomer = $this->serializer->denormalizeGuestCustomer($guestCustomerData);

        return $this->provider->create($guestCustomer);
    }
}
